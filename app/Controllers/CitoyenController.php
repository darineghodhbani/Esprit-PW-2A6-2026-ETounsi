<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Citoyen.php';
require_once __DIR__ . '/AuditController.php';
require_once __DIR__ . '/../GoogleSignIn.php';
require_once __DIR__ . '/GoogleController.php';

class CitoyenController extends Controller {
    public const ROLES = ['citoyen' => 'Citoyen', 'admin' => 'Administrateur'];
    public const ETATS = ['1' => 'Actif', '0' => 'Désactivé'];

    // Colonnes pour tous les SELECT
    public const COLONNES = 'id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, role, actif, version_session, created_at';
// pour laffichage
    public const CAPTCHA_SITE_KEY   = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    //pour la verification
    public const CAPTCHA_SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

    public function listerCitoyens() {
        $sql = 'SELECT ' . self::COLONNES . ' FROM citoyen ORDER BY nom, id_citoyen';
        $db = Database::getConnection();
        try {
            $query = $db->query($sql);
            $liste = [];
            foreach ($query->fetchAll() as $ligne) {
                $liste[] = $this->versTableau($ligne);
            }
            return $liste;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function recupererCitoyen($id) {
        $sql = 'SELECT ' . self::COLONNES . ' FROM citoyen WHERE id_citoyen = :id';
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $ligne = $query->fetch();
            return $ligne === false ? null : $this->versTableau($ligne);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Compte les utilisateurs d'un rôle (comme countByRole du workshop).
    public function compterParRole($role) {
        $sql = 'SELECT COUNT(*) FROM citoyen WHERE role = :role';
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['role' => $role]);
            return (int) $query->fetchColumn();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // CREATE : ajoute un utilisateur (comme addPersonne du cours).
    // Le mot de passe est haché avec password_hash avant d'être enregistré.
    public function ajouterCitoyen(Citoyen $citoyen) {
        $sql = "INSERT INTO citoyen (nom, prenom, telephone, email, mot_de_passe, role, actif)
                VALUES (:nom, :prenom, :telephone, :email, :mot_de_passe, :role, :actif)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $citoyen->getNom(),
                'prenom' => $citoyen->getPrenom(),
                'telephone' => $citoyen->getTelephone(),
                'email' => strtolower($citoyen->getEmail()),
                'mot_de_passe' => password_hash($citoyen->getMotDePasse(), PASSWORD_DEFAULT),
                'role' => $citoyen->getRole(),
                'actif' => $citoyen->getActif()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // UPDATE : modifie un utilisateur (comme updatePersonne du cours).
    // Si le champ mot de passe est vide, on garde l'ancien mot de passe.
    public function modifierCitoyen(Citoyen $citoyen, $id) {
        $sql = "UPDATE citoyen SET nom = :nom, prenom = :prenom, telephone = :telephone,
                email = :email, role = :role, actif = :actif WHERE id_citoyen = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'nom' => $citoyen->getNom(),
                'prenom' => $citoyen->getPrenom(),
                'telephone' => $citoyen->getTelephone(),
                'email' => strtolower($citoyen->getEmail()),
                'role' => $citoyen->getRole(),
                'actif' => $citoyen->getActif()
            ]);

            if ($citoyen->getMotDePasse() != '') {
                $query = $db->prepare('UPDATE citoyen SET mot_de_passe = :mot_de_passe WHERE id_citoyen = :id');
                $query->execute([
                    'id' => $id,
                    'mot_de_passe' => password_hash($citoyen->getMotDePasse(), PASSWORD_DEFAULT)
                ]);
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // DELETE : supprime un utilisateur (comme deletePersonne du cours).
    // Renvoie false si la base refuse (le compte a des colis, un dossier CNAM...).
    public function supprimerCitoyen($id) {
        $db = Database::getConnection();
        try {
            $query = $db->prepare('DELETE FROM citoyen WHERE id_citoyen = :id');
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function verifyLogin(string $email, string $motDePasse) {
        $resultat = $this->authentifier($email, $motDePasse);
        return $resultat['erreurs'] === [] ? $resultat['utilisateur'] : false;
    }

    public function findById(int $id) {
        return $this->trouver($id);
    }

    public function findByEmail(string $email) {
        $db = Database::getConnection();
        try {
            $sql = 'SELECT id_citoyen FROM citoyen WHERE email = :email';
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            $id = $query->fetchColumn();
            return $id === false ? false : $this->trouver((int) $id);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function versTableau($ligne) {
        return ['id' => (int) $ligne['id'], 'id_citoyen' => (int) $ligne['id'], 'nom' => $ligne['nom'],
                'prenom' => $ligne['prenom'] ?? '', 'telephone' => $ligne['telephone'] ?? '',
                'email' => $ligne['email'], 'role' => $ligne['role'], 'actif' => (int) $ligne['actif'],
                'version_session' => (int) $ligne['version_session'], 'created_at' => $ligne['created_at'],
                'date_inscription' => $ligne['date_inscription'] ?? $ligne['created_at']];
    }

    public function authentifier($email, $password)
    {
        $db = Database::getConnection();
        $email = strtolower($this->texte($email));
        $password = is_string($password) ? $password : '';
        $maintenant = time();
        $tentatives = [];
        foreach ($_SESSION['tentatives_connexion'] ?? [] as $date) {
            if ($date > $maintenant - 300) {
                $tentatives[] = $date;
            }
        }
        $_SESSION['tentatives_connexion'] = $tentatives;
        if (count($tentatives) >= 5) {
            return $this->resultat(['general' => 'Trop de tentatives. Attendez cinq minutes avant de réessayer.']);
        }
        $compte = false;
        $identifiantsValides = filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 150
            && strlen($password) <= 72 && strpos($password, "\0") === false;
        if ($identifiantsValides) {
            $sql = 'SELECT ' . self::COLONNES . ', mot_de_passe FROM citoyen WHERE email = :email LIMIT 1';
            $query = $db->prepare($sql);
            $query->execute(['email' => $email]);
            $compte = $query->fetch();
        }
        if (!$compte || (int) $compte['actif'] !== 1 || !password_verify($password, $compte['mot_de_passe'] ?? '')) {
            $_SESSION['tentatives_connexion'][] = $maintenant;
            return $this->resultat(['general' => 'Adresse email ou mot de passe incorrect.']);
        }
        unset($_SESSION['tentatives_connexion']);
        try {
            $db->beginTransaction();
            $compteVerifie = $this->lireCompte($compte['id'], true);
            if (!$compteVerifie || (int) $compteVerifie['actif'] !== 1
                || $compteVerifie['version_session'] !== $compte['version_session']
                || $compteVerifie['mot_de_passe'] !== $compte['mot_de_passe']) {
                $db->rollBack();
                return $this->resultat(['general' => 'Le compte a changé. Reconnectez-vous.']);
            }
            $utilisateur = $this->trouver($compte['id']);
            (new AuditController($db))->log($compte['id'], 'LOGIN', 'Connexion réussie.', $compte['id']);
            $db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    public function lister() {
        return $this->listerCitoyens();
    }

    public function trouver($id) {
        return $this->recupererCitoyen($id);
    }

    public function modifierProfil($id, $nom, $email, $password, $confirmation, $motDePasseActuel, $versionAttendue)
    {
        $db = Database::getConnection();
        $nom = $this->texte($nom);
        $email = strtolower($this->texte($email));
        $password = is_string($password) ? $password : '';
        $confirmation = is_string($confirmation) ? $confirmation : '';
        $motDePasseActuel = is_string($motDePasseActuel) ? $motDePasseActuel : '';
        $erreurs = $this->validerCompte($id, $nom, $email, $password, $confirmation);
        if ($erreurs !== []) {
            return $this->resultat($erreurs);
        }

        try {
            $db->beginTransaction();
            $compte = $this->lireCompte($id, true);
            if ($compte === false || (int) $compte['actif'] !== 1) {
                $db->rollBack();
                return $this->resultat(['general' => 'Ce compte est indisponible.']);
            }
            // La version provient de la session vérifiée avant le traitement du formulaire.
            if ((int) $compte['version_session'] !== (int) $versionAttendue) {
                $db->rollBack();
                return $this->resultat(['general' => 'Votre compte a été modifié. Reconnectez-vous avant de modifier votre profil.']);
            }
            $identifiantsModifies = $password !== '' || $email !== $compte['email'];
            $motDePasseActuelValide = strlen($motDePasseActuel) <= 72 && strpos($motDePasseActuel, "\0") === false;
            if ($identifiantsModifies && (!$motDePasseActuelValide || !password_verify($motDePasseActuel, $compte['mot_de_passe'] ?? ''))) {
                $db->rollBack();
                return $this->resultat(['mot_de_passe_actuel' => 'Le mot de passe actuel est incorrect.']);
            }
            $motDePasse = $compte['mot_de_passe'];
            if ($password !== '') {
                $motDePasse = password_hash($password, PASSWORD_DEFAULT);
            }
            $version = (int) $compte['version_session'];
            if ($identifiantsModifies) {
                $version++;
            }
            // Le profil personnel conserve le rôle et l'état définis par l'administration.
            $sql = 'UPDATE citoyen SET nom = :nom, email = :email, mot_de_passe = :mot_de_passe,
                 version_session = :version WHERE id_citoyen = :id';
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $nom, 'email' => $email, 'mot_de_passe' => $motDePasse,
                'version' => $version, 'id' => $id,
            ]);
            $utilisateur = $this->trouver($id);
            (new AuditController($db))->log($_SESSION['user_id'] ?? $id, 'USER_UPDATED', 'Profil mis à jour.', $id);
            $db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    private function validerCompte($id, $nom, $email, $password, $confirmation)
    {
        try {
            $erreurs = [];
            if (mb_strlen($nom, 'UTF-8') < 2 || mb_strlen($nom, 'UTF-8') > 80) {
                $erreurs['nom'] = 'Le nom doit contenir entre 2 et 80 caractères.';
            }
            if (preg_match('/[<>\x00-\x1F]/u', $nom)) {
                $erreurs['nom'] = 'Saisissez un nom sans balises ni caractères de contrôle.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
                $erreurs['email'] = 'Saisissez une adresse email valide de 150 caractères maximum.';
            }
            if ($id === null || $password !== '') {
                if (mb_strlen($password, 'UTF-8') < 8 || strlen($password) > 72) {
                    $erreurs['password'] = 'Le mot de passe doit contenir au moins 8 caractères et au maximum 72 octets.';
                }
                if (strpos($password, "\0") !== false) {
                    $erreurs['password'] = 'Le mot de passe contient un caractère invalide.';
                }
            }
            if ($password !== $confirmation) {
                $erreurs['confirmation'] = 'Les mots de passe doivent être identiques.';
            }
            if ($this->emailDejaUtilise($email, $id)) {
                $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
            }
            return $erreurs;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Vrai si un autre compte utilise déjà cet email.
    private function emailDejaUtilise($email, $id)
    {
        $db = Database::getConnection();
        try {
            $sql = 'SELECT id_citoyen FROM citoyen WHERE email = :email AND id_citoyen <> :id LIMIT 1';
            $query = $db->prepare($sql);
            $query->execute(['email' => $email, 'id' => (int) $id]);
            return $query->fetch() !== false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function lireCompte($id, $verrouiller = false)
    {
        $db = Database::getConnection();
        try {
            $sql = 'SELECT ' . self::COLONNES . ', mot_de_passe FROM citoyen WHERE id_citoyen = :id';
            if ($verrouiller) {
                $sql .= ' FOR UPDATE';
            }
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function texte($valeur)
    {
        return is_string($valeur) ? trim($valeur) : '';
    }

    private function resultat($erreurs = [], $utilisateur = null)
    {
        return [
            'erreurs' => $erreurs,
            'utilisateur' => $utilisateur,
            'id' => $utilisateur === null ? null : $utilisateur['id'],
        ];
    }

    private function erreurBase($erreur)
    {
        $db = Database::getConnection();
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        if (isset($erreur->errorInfo[1]) && (int) $erreur->errorInfo[1] === 1062) {
            return $this->resultat(['email' => 'Cette adresse email est déjà utilisée.']);
        }
        if (isset($erreur->errorInfo[1]) && (int) $erreur->errorInfo[1] === 1451) {
            return $this->resultat(['general' => 'Ce compte est lié à des données du projet. Désactivez-le pour conserver leur historique.']);
        }
        error_log($erreur->getMessage());
        return $this->resultat(['general' => 'L’enregistrement est indisponible. Réessayez dans un instant.']);
    }

    public static function clean(array $post): array {
        $password = $post['password'] ?? $post['mot_de_passe'] ?? '';
        $confirmation = $post['confirmation'] ?? $post['mot_de_passe2'] ?? '';
        return [
            'nom' => posteText($post, 'nom'), 'prenom' => posteText($post, 'prenom'),
            'email' => posteText($post, 'email'), 'telephone' => posteText($post, 'telephone'),
            'role' => posteText($post, 'role'), 'actif' => posteText($post, 'actif'),
            'password' => is_string($password) ? $password : '',
            'confirmation' => is_string($confirmation) ? $confirmation : '',
        ];
    }

    // Contrôle de saisie PHP (les mêmes règles que le JavaScript).
    // $creation = true : le mot de passe est obligatoire (ajout / inscription).
    public function verifierCitoyen(array $d, bool $creation = true): array {
        $erreurs = [];

        if (strlen($d['nom']) < 2 || strlen($d['nom']) > 50) {
            $erreurs['nom'] = 'Le nom doit contenir entre 2 et 50 caractères.';
        }
        if (strlen($d['prenom']) < 2 || strlen($d['prenom']) > 50) {
            $erreurs['prenom'] = 'Le prénom doit contenir entre 2 et 50 caractères.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'L’adresse email n’est pas valide.';
        }
        if (strlen($d['telephone']) != 8 || !ctype_digit($d['telephone'])) {
            $erreurs['telephone'] = 'Le téléphone doit contenir 8 chiffres.';
        }
        if ($creation || $d['password'] != '') {
            if (strlen($d['password']) < 8) {
                $erreurs['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }
        }
        if ($d['password'] != $d['confirmation']) {
            $erreurs['confirmation'] = 'Les deux mots de passe ne sont pas identiques.';
        }
        if (!isset(self::ROLES[$d['role']])) {
            $erreurs['role'] = 'Choisissez un rôle de la liste.';
        }
        if ($d['actif'] != '0' && $d['actif'] != '1') {
            $erreurs['actif'] = 'Choisissez un état de la liste.';
        }

        return $erreurs;
    }

    public function verifierCaptcha(): bool {
        if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify'
             . '?secret=' . self::CAPTCHA_SECRET_KEY
             . '&response=' . urlencode($_POST['g-recaptcha-response']);

        $reponse = file_get_contents($url);
        $resultat = json_decode($reponse, true);

        return $resultat['success'] == true;
    }

    public function signup(): void {
        $erreurs = [];
        $old = self::clean([]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['role'] = 'citoyen';
            $old['actif'] = '1';
            $erreurs = $this->verifierCitoyen($old, true);
            if (!$this->verifierCaptcha()) {
                $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            }
            if ($this->emailDejaUtilise($old['email'], null)) {
                $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
            }
            if (count($erreurs) == 0) {
                $citoyen = new Citoyen(null, $old['nom'], $old['email'], $old['password'], 'citoyen', 1, $old['prenom'], $old['telephone'], null);
                $this->ajouterCitoyen($citoyen);
                auditEvenement('USER_CREATED', 'Inscription de ' . $old['email'] . '.');
                header('Location: index.php?page=login&inscrit=1', true, 303);
                exit;
            }
        }
        $this->renderFront('signup', ['title' => 'Inscription — E-Tounsi', 'currentPage' => 'signup', 'old' => $old, 'erreurs' => $erreurs]);
    }

    public function login(): void {
        $erreurs = [];
        $old = ['email' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $erreurs['email'] = 'L’email n’est pas valide.';
            if ($old['password'] === '') $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
            if (!$this->verifierCaptcha()) {
                $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            }
            if (count($erreurs) == 0) {
                $citoyen = $this->verifyLogin($old['email'], $old['password']);
                if ($citoyen) {
                    connecterUtilisateur($citoyen);
                    // L'admin va au back office, le citoyen va à l'accueil.
                    if ($citoyen['role'] === 'admin') {
                        header('Location: index.php?page=dashboard', true, 303);
                    } else {
                        header('Location: index.php?page=home', true, 303);
                    }
                    exit;
                }
                $erreurs['global'] = 'Email ou mot de passe incorrect.';
            }
            $compte = filter_var($old['email'], FILTER_VALIDATE_EMAIL) ? $this->findByEmail($old['email']) : false;
            $raison = isset($erreurs['captcha']) ? 'CAPTCHA non validé.' : 'Identifiants non valides ou compte indisponible.';
            auditEvenement('LOGIN_FAILED', $raison, $compte ? $compte['id'] : null);
        }
        $this->renderFront('login', ['title' => 'Connexion — E-Tounsi', 'currentPage' => 'login', 'old' => $old, 'erreurs' => $erreurs]);
    }

    public function logout(): void {
        deconnecterUtilisateur('Déconnexion demandée par l’utilisateur.');
        header('Location: index.php?page=home', true, 303);
        exit;
    }

    public function profile(): void {
        $citoyen = exigerConnexion();
        $erreurs = [];
        $old = ['nom' => $citoyen['nom'], 'email' => $citoyen['email'], 'mot_de_passe_actuel' => '', 'password' => '', 'confirmation' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $valeur) {
                if (isset($_POST[$champ]) && is_string($_POST[$champ])) $old[$champ] = $_POST[$champ];
            }
            $resultat = $this->modifierProfil($citoyen['id'], $old['nom'], $old['email'],
                $old['password'], $old['confirmation'], $old['mot_de_passe_actuel'], $citoyen['version_session']);
            $erreurs = $resultat['erreurs'];
            if (count($erreurs) == 0) {
                connecterUtilisateur($resultat['utilisateur']);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre profil a été mis à jour.'];
                header('Location: index.php?page=profile', true, 303);
                exit;
            }
            http_response_code(422);
        }
        $googleReady = $citoyen['role'] === 'citoyen' && GoogleSignIn::configured();
        $googleLinked = $googleReady && (new GoogleController())->liee($citoyen['id']);
        $this->renderFront('profile', ['title' => t('profil.titre'), 'currentPage' => 'profile', 'citoyen' => $citoyen,
            'old' => $old, 'erreurs' => $erreurs, 'googleReady' => $googleReady, 'googleLinked' => $googleLinked]);
    }

    public static function requireLogin(): void {
        exigerConnexion();
    }

    public static function requireAdmin(): void {
        exigerRole(['admin']);
    }

    // BACK OFFICE : le Controller traite, puis la vue affiche
    

    public function listBack(): void {
        self::requireAdmin();
        $recherche = posteText($_GET, 'q');
        $liste = [];
        foreach ($this->listerCitoyens() as $citoyen) {
            $texte = $citoyen['nom'] . ' ' . $citoyen['prenom'] . ' ' . $citoyen['email'] . ' ' . $citoyen['telephone'];
            if (posteMatches($recherche, $texte)) $liste[] = $citoyen;
        }
        $this->renderBack('utilisateurs/userList', [
            'title' => 'Gestion des utilisateurs', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
            'liste' => $liste, 'recherche' => $recherche, 'roles' => self::ROLES, 'csrfToken' => csrfToken(),
            // Statistiques par rôle
            'nbCitoyens' => $this->compterParRole('citoyen'),
            'nbAdmins' => $this->compterParRole('admin'),
        ]);
    }

    public function addBack(): void {
        self::requireAdmin();
        $erreurs = [];
        $old = self::clean([]);
        $old['role'] = 'citoyen';
        $old['actif'] = '1';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $erreurs = $this->verifierCitoyen($old);
            if ($this->emailDejaUtilise($old['email'], null)) {
                $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
            }
            if (count($erreurs) == 0) {
                $citoyen = new Citoyen(null, $old['nom'], $old['email'], $old['password'], $old['role'], (int) $old['actif'], $old['prenom'], $old['telephone'], null);
                $this->ajouterCitoyen($citoyen);
                auditEvenement('USER_CREATED', 'Création du compte ' . $old['email'] . '.');
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été ajouté.'];
                header('Location: index.php?page=admin-utilisateurs', true, 303);
                exit;
            }
            http_response_code(422);
        }

        $this->renderBack('utilisateurs/addUser', [
            'title' => 'Ajouter un utilisateur', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
            'old' => $old, 'erreurs' => $erreurs, 'csrfToken' => csrfToken(),
        ]);
    }

    public function updateBack(): void {
        $compteConnecte = exigerRole(['admin']);
        $id = posteId($_GET) ?? posteId($_POST);
        $old = $id === null ? null : $this->recupererCitoyen($id);
        $ancien = $old; // le compte avant la modification
        if (!$old) {
            http_response_code(404);
            $this->renderBack('commun/problem', [
                'title' => 'Utilisateur introuvable', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
                'message' => 'Ce compte n’existe plus ou le lien est incorrect.',
            ]);
            return;
        }
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id'] = $id;
            $erreurs = $this->verifierCitoyen($old, false);
            if ($this->emailDejaUtilise($old['email'], $id)) {
                $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
            }
            // L'admin ne peut pas retirer son propre rôle admin ni désactiver son propre compte.
            if ($id === $compteConnecte['id'] && ($old['role'] !== 'admin' || $old['actif'] !== '1')) {
                $erreurs['role'] = 'Vous ne pouvez pas retirer votre rôle administrateur ni désactiver votre propre compte.';
            }
            if (count($erreurs) == 0) {
                $citoyen = new Citoyen($id, $old['nom'], $old['email'], $old['password'], $old['role'], (int) $old['actif'], $old['prenom'], $old['telephone'], null);
                $this->modifierCitoyen($citoyen, $id);

                // Journal d'audit
                auditEvenement('USER_UPDATED', 'Compte #' . $id . ' mis à jour.', $id);
                if ($ancien['role'] !== $old['role']) {
                    auditEvenement('ROLE_CHANGED', 'Rôle changé de ' . $ancien['role'] . ' à ' . $old['role'] . '.', $id);
                }
                if ($ancien['actif'] === 1 && $old['actif'] === '0') {
                    auditEvenement('ACCOUNT_SUSPENDED', 'Compte suspendu.', $id);
                }
                if ($ancien['actif'] === 0 && $old['actif'] === '1') {
                    auditEvenement('ACCOUNT_ACTIVATED', 'Compte activé.', $id);
                }

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été mis à jour.'];
                header('Location: index.php?page=admin-utilisateurs', true, 303);
                exit;
            }
            http_response_code(422);
        }

        $this->renderBack('utilisateurs/updateUser', [
            'title' => 'Modifier un utilisateur', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
            'id' => $id, 'old' => $old, 'erreurs' => $erreurs, 'csrfToken' => csrfToken(),
        ]);
    }

    
    public function deleteBack(): void {
        self::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {
            $id = posteId($_POST);
            if ($id === null) {
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Identifiant utilisateur invalide.'];
            } elseif ($id === (int) $_SESSION['user_id']) {
                // L'admin ne peut pas supprimer son propre compte.
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'];
            } elseif ($this->supprimerCitoyen($id)) {
                auditEvenement('USER_DELETED', 'Compte #' . $id . ' supprimé.', $id);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été supprimé.'];
            } else {
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Ce compte est lié à des données (colis, dossier CNAM...). Désactivez-le au lieu de le supprimer.'];
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
        }
        header('Location: index.php?page=admin-utilisateurs', true, 303);
        exit;
    }
}
