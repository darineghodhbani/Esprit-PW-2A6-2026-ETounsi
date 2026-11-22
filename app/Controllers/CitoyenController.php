<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Citoyen.php';
require_once __DIR__ . '/AuditController.php';

class CitoyenController extends Controller {
    public const ROLES = ['citoyen' => 'Citoyen', 'admin' => 'Administrateur'];
    public const ETATS = ['1' => 'Actif', '0' => 'Désactivé'];
    public const CAPTCHA_SITE_KEY = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    public const CAPTCHA_SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

    public function listerCitoyens() {
        return $this->lister();
    }

    public function recupererCitoyen($id) {
        return $this->trouver($id);
    }

    public function ajouterCitoyen(Citoyen $citoyen, $confirmation) {
        return $this->enregistrer(null, $citoyen->getNom(), $citoyen->getEmail(), $citoyen->getMotDePasse(),
            $confirmation, $citoyen->getRole(), $citoyen->getActif(), null, $citoyen->getPrenom(), $citoyen->getTelephone());
    }

    public function modifierCitoyen(Citoyen $citoyen, $id, $confirmation) {
        return $this->enregistrer($id, $citoyen->getNom(), $citoyen->getEmail(), $citoyen->getMotDePasse(),
            $confirmation, $citoyen->getRole(), $citoyen->getActif(), $citoyen->getVersionSession(), $citoyen->getPrenom(), $citoyen->getTelephone());
    }

    public function supprimerCitoyen($id) {
        return $this->supprimer($id);
    }

    public function create(string $nom, string $prenom, string $email, string $telephone, string $motDePasse) {
        $resultat = $this->enregistrer(null, $nom, $email, $motDePasse, $motDePasse, 'citoyen', 1, null, $prenom, $telephone);
        return $resultat['erreurs'] === [] ? $resultat['id'] : false;
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
        $requete = $db->prepare('SELECT id_citoyen FROM citoyen WHERE email = :email');
        $requete->execute(['email' => $email]);
        $id = $requete->fetchColumn();
        return $id === false ? false : $this->trouver((int) $id);
    }

    private function versTableau($ligne) {
        return ['id' => (int) $ligne['id'], 'id_citoyen' => (int) $ligne['id'], 'nom' => $ligne['nom'],
                'prenom' => $ligne['prenom'] ?? '', 'telephone' => $ligne['telephone'] ?? '',
                'email' => $ligne['email'], 'role' => $ligne['role'], 'actif' => (int) $ligne['actif'],
                'version_session' => (int) $ligne['version_session'], 'created_at' => $ligne['created_at'],
                'date_inscription' => $ligne['date_inscription'] ?? $ligne['created_at']];
    }

    public function inscrire($nom, $email, $password, $confirmation)
    {
        return $this->enregistrer(null, $nom, $email, $password, $confirmation, 'citoyen', 1);
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
            $requete = $db->prepare(
                'SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, mot_de_passe, role, actif, version_session, created_at
                 FROM citoyen WHERE email = :email LIMIT 1'
            );
            $requete->execute(['email' => $email]);
            $compte = $requete->fetch();
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

    public function lister()
    {
        $db = Database::getConnection();
        $requete = $db->query(
            'SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, role, actif, version_session, created_at
             FROM citoyen ORDER BY nom, id_citoyen'
        );
        $utilisateurs = [];
        foreach ($requete->fetchAll() as $ligne) {
            $utilisateurs[] = $this->versTableau($ligne);
        }
        return $utilisateurs;
    }

    public function trouver($id)
    {
        $ligne = $this->lireCompte($id);
        if ($ligne === false) {
            return null;
        }
        return $this->versTableau($ligne);
    }

    public function enregistrer($id, $nom, $email, $password, $confirmation, $role, $actif, $versionAttendue = null, $prenom = null, $telephone = null)
    {
        $db = Database::getConnection();
        $nom = $this->texte($nom);
        $email = strtolower($this->texte($email));
        $password = is_string($password) ? $password : '';
        $confirmation = is_string($confirmation) ? $confirmation : '';
        $erreurs = $this->validerCompte($id, $nom, $email, $password, $confirmation);
        if (!is_string($role) || !isset(self::ROLES[$role])) {
            $erreurs['role'] = 'Choisissez un rôle proposé dans la liste.';
        }
        if (!in_array($actif, [0, 1, '0', '1'], true)) {
            $erreurs['actif'] = 'Choisissez un état de compte valide.';
        }
        if ($erreurs !== []) {
            return $this->resultat($erreurs);
        }

        $creation = $id === null;
        try {
            $db->beginTransaction();
            // Ces lignes restent verrouillées jusqu'à la fin de la modification.
            $administrateurs = $this->verrouillerAdministrateurs();
            if ($id === null) {
                $requete = $db->prepare(
                    'INSERT INTO citoyen (nom, prenom, telephone, email, mot_de_passe, role, actif)
                     VALUES (:nom, :prenom, :telephone, :email, :mot_de_passe, :role, :actif)'
                );
                $requete->execute([
                    'nom' => $nom,
                    'prenom' => $prenom ?? '', 'telephone' => $telephone,
                    'email' => $email,
                    'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'actif' => (int) $actif,
                ]);
                $id = (int) $db->lastInsertId();
            } else {
                $compte = $this->lireCompte($id, true);
                if ($compte === false) {
                    $db->rollBack();
                    return $this->resultat(['general' => 'Cet utilisateur n’existe plus.']);
                }
                if ($versionAttendue !== null && (int) $compte['version_session'] !== (int) $versionAttendue) {
                    $db->rollBack();
                    return $this->resultat(['general' => 'Votre compte a été modifié. Reconnectez-vous avant de modifier votre profil.']);
                }
                if ($compte['role'] === 'admin' && (int) $compte['actif'] === 1
                    && ($role !== 'admin' || (int) $actif !== 1) && count($administrateurs) <= 1) {
                    $db->rollBack();
                    return $this->resultat(['general' => 'Conservez au moins un administrateur actif.']);
                }
                $motDePasse = $compte['mot_de_passe'];
                $version = (int) $compte['version_session'];
                if ($password !== '') {
                    $motDePasse = password_hash($password, PASSWORD_DEFAULT);
                }
                if ($password !== '' || $role !== $compte['role'] || (int) $actif !== (int) $compte['actif']) {
                    $version++;
                }
                $requete = $db->prepare(
                    'UPDATE citoyen SET nom = :nom, prenom = :prenom, telephone = :telephone, email = :email,
                     mot_de_passe = :mot_de_passe, role = :role, actif = :actif, version_session = :version WHERE id_citoyen = :id'
                );
                $requete->execute([
                    'nom' => $nom, 'prenom' => $prenom ?? $compte['prenom'],
                    'telephone' => $telephone ?? $compte['telephone'], 'email' => $email, 'mot_de_passe' => $motDePasse,
                    'role' => $role, 'actif' => (int) $actif, 'version' => $version, 'id' => $id,
                ]);
            }
            $utilisateur = $this->trouver($id);
            $audit = new AuditController($db);
            $acteur = $_SESSION['user_id'] ?? ($creation ? $id : null);
            $audit->log($acteur, $creation ? 'USER_CREATED' : 'USER_UPDATED',
                $creation ? 'Création du compte #' . $id . '.' : 'Compte #' . $id . ' mis à jour.', $id);
            if (!$creation && $role !== $compte['role']) {
                $audit->log($acteur, 'ROLE_CHANGED', 'Rôle changé de ' . $compte['role'] . ' à ' . $role . '.', $id);
            }
            if (!$creation && (int) $actif !== (int) $compte['actif']) {
                $audit->log($acteur, (int) $actif === 1 ? 'ACCOUNT_ACTIVATED' : 'ACCOUNT_SUSPENDED',
                    (int) $actif === 1 ? 'Compte activé.' : 'Compte suspendu.', $id);
            }
            $db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
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
            $requete = $db->prepare(
                'UPDATE citoyen SET nom = :nom, email = :email, mot_de_passe = :mot_de_passe,
                 version_session = :version WHERE id_citoyen = :id'
            );
            $requete->execute([
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

    public function supprimer($id)
    {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            $administrateurs = $this->verrouillerAdministrateurs();
            $compte = $this->lireCompte($id, true);
            if ($compte === false) {
                $db->rollBack();
                return $this->resultat(['general' => 'Cet utilisateur n’existe plus.']);
            }
            if ($compte['role'] === 'admin' && (int) $compte['actif'] === 1 && count($administrateurs) <= 1) {
                $db->rollBack();
                return $this->resultat(['general' => 'Conservez au moins un administrateur actif.']);
            }
            $requete = $db->prepare('SELECT (SELECT COUNT(*) FROM colis WHERE id_citoyen = :colis) + (SELECT COUNT(*) FROM propriete WHERE id_citoyen = :propriete)');
            $requete->execute(['colis' => $id, 'propriete' => $id]);
            if ((int) $requete->fetchColumn() > 0) {
                $db->rollBack();
                return $this->resultat(['general' => 'Ce compte possède des colis ou des propriétés. Désactivez-le pour conserver leur historique.']);
            }
            (new AuditController($db))->log($_SESSION['user_id'] ?? null, 'USER_DELETED', 'Compte #' . $id . ' supprimé.', $id);
            $requete = $db->prepare('DELETE FROM citoyen WHERE id_citoyen = :id');
            $requete->execute(['id' => $id]);
            $db->commit();
            return ['erreurs' => [], 'utilisateur' => null, 'id' => (int) $id];
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    private function validerCompte($id, $nom, $email, $password, $confirmation)
    {
        $db = Database::getConnection();
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
        $requete = $db->prepare(
            'SELECT id_citoyen FROM citoyen WHERE email = :email AND id_citoyen <> :id LIMIT 1'
        );
        $requete->execute(['email' => $email, 'id' => (int) $id]);
        if ($requete->fetch()) {
            $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
        }
        return $erreurs;
    }

    private function lireCompte($id, $verrouiller = false)
    {
        $db = Database::getConnection();
        $sql = 'SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, mot_de_passe, role, actif, version_session, created_at
                FROM citoyen WHERE id_citoyen = :id';
        if ($verrouiller) {
            $sql .= ' FOR UPDATE';
        }
        $requete = $db->prepare($sql);
        $requete->execute(['id' => $id]);
        return $requete->fetch();
    }

    private function verrouillerAdministrateurs()
    {
        $db = Database::getConnection();
        $requete = $db->query(
            "SELECT id_citoyen FROM citoyen WHERE role = 'admin' AND actif = 1 ORDER BY id_citoyen FOR UPDATE"
        );
        return $requete->fetchAll();
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

    public function verifierCitoyen(array $d, bool $creation = true, bool $inscription = false): array {
        $erreurs = [];
        $limiteNom = $inscription ? 50 : 80;
        if (mb_strlen($d['nom']) < 2 || mb_strlen($d['nom']) > $limiteNom || preg_match('/[<>\x00-\x1F]/u', $d['nom'])) {
            $erreurs['nom'] = 'Saisissez un nom de 2 à ' . $limiteNom . ' caractères, sans balises ni caractères de contrôle.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || strlen($d['email']) > 150) $erreurs['email'] = 'Saisissez une adresse email valide.';
        if (!isset(self::ROLES[$d['role']])) $erreurs['role'] = 'Choisissez un rôle proposé dans la liste.';
        if (!in_array($d['actif'], ['0', '1'], true)) $erreurs['actif'] = 'Choisissez un état de compte valide.';
        $champPassword = $inscription ? 'mot_de_passe' : 'password';
        $champConfirmation = $inscription ? 'mot_de_passe2' : 'confirmation';
        if ($creation || $d['password'] !== '') {
            if (mb_strlen($d['password']) < 8 || strlen($d['password']) > 72 || strpos($d['password'], "\0") !== false) {
                $erreurs[$champPassword] = 'Le mot de passe doit contenir au moins 8 caractères et au maximum 72 octets.';
            }
        }
        if ($d['password'] !== $d['confirmation']) $erreurs[$champConfirmation] = 'Les mots de passe doivent être identiques.';
        if (mb_strlen($d['prenom']) < 2 || mb_strlen($d['prenom']) > 50 || preg_match('/[<>\x00-\x1F]/u', $d['prenom'])) {
            $erreurs['prenom'] = 'Saisissez un prénom de 2 à 50 caractères, sans balises ni caractères de contrôle.';
        }
        if ($inscription) {
            if ($d['telephone'] !== '' && (strlen($d['telephone']) !== 8 || !ctype_digit($d['telephone']))) $erreurs['telephone'] = 'Le téléphone doit contenir 8 chiffres.';
        } elseif (!preg_match('/^[2-9][0-9]{7}$/D', $d['telephone'])) {
            $erreurs['telephone'] = 'Le téléphone doit contenir 8 chiffres et commencer par un chiffre de 2 à 9.';
        }
        return $erreurs;
    }

    public function verifierCaptcha(): bool {
        $token = posteText($_POST, 'g-recaptcha-response');
        if ($token === '') return false;
        $url = 'https://www.google.com/recaptcha/api/siteverify'
             . '?secret=' . self::CAPTCHA_SECRET_KEY . '&response=' . urlencode($token);
        $contexte = stream_context_create(['http' => ['timeout' => 8]]);
        $reponse = @file_get_contents($url, false, $contexte);
        if ($reponse === false) return false;
        $resultat = json_decode($reponse, true);
        return is_array($resultat) && !empty($resultat['success']);
    }

    public function signup(): void {
        $erreurs = [];
        $old = self::clean([]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['role'] = 'citoyen';
            $old['actif'] = '1';
            $erreurs = $this->verifierCitoyen($old, true, true);
            if (!$this->verifierCaptcha()) $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            if (count($erreurs) == 0) {
                $citoyen = new Citoyen(null, $old['nom'], $old['email'], $old['password'], 'citoyen', 1, $old['prenom'], $old['telephone']);
                $resultat = $this->ajouterCitoyen($citoyen, $old['confirmation']);
                $erreurs = $resultat['erreurs'];
                if (count($erreurs) == 0) {
                    header('Location: index.php?page=login&inscrit=1', true, 303);
                    exit;
                }
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
            if (!$this->verifierCaptcha()) $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            if (count($erreurs) == 0) {
                $citoyen = $this->verifyLogin($old['email'], $old['password']);
                if ($citoyen) {
                    connecterUtilisateur($citoyen);
                    header('Location: index.php?page=' . ($citoyen['role'] === 'citoyen' ? 'home' : 'dashboard'), true, 303);
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
        $this->renderFront('profile', ['title' => t('profil.titre'), 'currentPage' => 'profile', 'citoyen' => $citoyen, 'old' => $old, 'erreurs' => $erreurs]);
    }

    public static function requireLogin(): void {
        exigerConnexion();
    }

    public static function requireAdmin(): void {
        exigerRole(['admin']);
    }

    public function listBack(): void {
        $this->renderBack('utilisateurs/userList');
    }

    public function addBack(): void {
        $this->renderBack('utilisateurs/addUser');
    }

    public function updateBack(): void {
        $this->renderBack('utilisateurs/updateUser');
    }

    public function deleteBack(): void {
        require __DIR__ . '/../Views/backoffice/pages/utilisateurs/deleteUser.php';
    }
}
