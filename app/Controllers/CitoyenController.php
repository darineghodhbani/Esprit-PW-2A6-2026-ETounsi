<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Citoyen.php';
require_once __DIR__ . '/AuditController.php';
require_once __DIR__ . '/../GoogleSignIn.php';
require_once __DIR__ . '/GoogleController.php';

class CitoyenController extends Controller {
    public const ROLES = ['citoyen' => 'Citoyen', 'agent' => 'Agent', 'admin' => 'Administrateur'];
    public const ETATS = ['1' => 'Actif', '0' => 'Désactivé'];

    private static function captchaConfiguration(): array {
        $local = is_file(__DIR__ . '/../../config.local.php') ? require __DIR__ . '/../../config.local.php' : [];
        return [
            'site_key' => is_string($local['recaptcha_site_key'] ?? null) ? trim($local['recaptcha_site_key']) : '',
            'secret_key' => is_string($local['recaptcha_secret_key'] ?? null) ? trim($local['recaptcha_secret_key']) : '',
            'environment' => getenv('APP_ENV') ?: ($local['app_env'] ?? 'production'),
        ];
    }

    public static function captchaEnabled(): bool {
        $config = self::captchaConfiguration();
        return $config['site_key'] !== '' && $config['secret_key'] !== '';
    }

    public static function captchaSiteKey(): string {
        $config = self::captchaConfiguration();
        return self::captchaEnabled() ? $config['site_key'] : '';
    }

    public function listerCitoyens() {
        $sql = "SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, role, actif, version_session, created_at
                FROM citoyen ORDER BY nom, id_citoyen";
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
        $sql = "SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, role, actif, version_session, created_at
                FROM citoyen WHERE id_citoyen = :id";
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

    public function ajouterCitoyen(Citoyen $citoyen, $confirmation) {
        $sql = "INSERT INTO citoyen (nom, prenom, telephone, email, mot_de_passe, role, actif)
                VALUES (:nom, :prenom, :telephone, :email, :mot_de_passe, :role, :actif)";
        $db = Database::getConnection();
        $nom = $this->texte($citoyen->getNom());
        $email = strtolower($this->texte($citoyen->getEmail()));
        $password = $citoyen->getMotDePasse() ?? '';
        $role = $citoyen->getRole();
        $actif = $citoyen->getActif();
        $erreurs = $this->validerCompte(null, $nom, $email, $password, $confirmation);
        if (!is_string($role) || !isset(self::ROLES[$role])) $erreurs['role'] = 'Choisissez un rôle proposé dans la liste.';
        if (!in_array($actif, [0, 1], true)) $erreurs['actif'] = 'Choisissez un état de compte valide.';
        if ($erreurs !== []) return $this->resultat($erreurs);

        try {
            $db->beginTransaction();
            $this->verrouillerAdministrateurs();
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $nom, 'prenom' => $citoyen->getPrenom() ?? '',
                'telephone' => $citoyen->getTelephone(), 'email' => $email,
                'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role, 'actif' => $actif,
            ]);
            $id = (int) $db->lastInsertId();
            $utilisateur = $this->recupererCitoyen($id);
            $acteur = $_SESSION['user_id'] ?? $id;
            (new AuditController($db))->log($acteur, 'USER_CREATED', 'Création du compte #' . $id . '.', $id);
            $db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $e) {
            return $this->erreurBase($e);
        }
    }

    public function modifierCitoyen(Citoyen $citoyen, $id, $confirmation) {
        $sql = "UPDATE citoyen SET nom = :nom, prenom = :prenom, telephone = :telephone, email = :email,
                mot_de_passe = :mot_de_passe, role = :role, actif = :actif, version_session = :version WHERE id_citoyen = :id";
        $db = Database::getConnection();
        $nom = $this->texte($citoyen->getNom());
        $email = strtolower($this->texte($citoyen->getEmail()));
        $password = $citoyen->getMotDePasse() ?? '';
        $role = $citoyen->getRole();
        $actif = $citoyen->getActif();
        $erreurs = $this->validerCompte($id, $nom, $email, $password, $confirmation);
        if (!is_string($role) || !isset(self::ROLES[$role])) $erreurs['role'] = 'Choisissez un rôle proposé dans la liste.';
        if (!in_array($actif, [0, 1], true)) $erreurs['actif'] = 'Choisissez un état de compte valide.';
        if ($erreurs !== []) return $this->resultat($erreurs);

        try {
            $db->beginTransaction();
            $administrateurs = $this->verrouillerAdministrateurs();
            $compte = $this->lireCompte($id, true);
            if ($compte === false) {
                $db->rollBack();
                return $this->resultat(['general' => 'Cet utilisateur n’existe plus.']);
            }
            $versionAttendue = $citoyen->getVersionSession();
            if ($versionAttendue !== null && (int) $compte['version_session'] !== $versionAttendue) {
                $db->rollBack();
                return $this->resultat(['general' => 'Votre compte a été modifié. Reconnectez-vous avant de modifier votre profil.']);
            }
            if ($compte['role'] === 'admin' && (int) $compte['actif'] === 1
                && ($role !== 'admin' || $actif !== 1) && count($administrateurs) <= 1) {
                $db->rollBack();
                return $this->resultat(['general' => 'Conservez au moins un administrateur actif.']);
            }
            $motDePasse = $password === '' ? $compte['mot_de_passe'] : password_hash($password, PASSWORD_DEFAULT);
            $version = (int) $compte['version_session'];
            if ($password !== '' || $role !== $compte['role'] || $actif !== (int) $compte['actif']) $version++;
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $nom, 'prenom' => $citoyen->getPrenom() ?? $compte['prenom'],
                'telephone' => $citoyen->getTelephone() ?? $compte['telephone'], 'email' => $email,
                'mot_de_passe' => $motDePasse, 'role' => $role, 'actif' => $actif, 'version' => $version, 'id' => $id,
            ]);
            $utilisateur = $this->recupererCitoyen($id);
            $audit = new AuditController($db);
            $acteur = $_SESSION['user_id'] ?? null;
            $audit->log($acteur, 'USER_UPDATED', 'Compte #' . $id . ' mis à jour.', $id);
            if ($role !== $compte['role']) $audit->log($acteur, 'ROLE_CHANGED', 'Rôle changé de ' . $compte['role'] . ' à ' . $role . '.', $id);
            if ($actif !== (int) $compte['actif']) {
                $audit->log($acteur, $actif === 1 ? 'ACCOUNT_ACTIVATED' : 'ACCOUNT_SUSPENDED', $actif === 1 ? 'Compte activé.' : 'Compte suspendu.', $id);
            }
            $db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $e) {
            return $this->erreurBase($e);
        }

    }

    public function supprimerCitoyen($id)
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
            $sql = 'SELECT (SELECT COUNT(*) FROM colis WHERE id_citoyen = :colis) + (SELECT COUNT(*) FROM propriete WHERE id_citoyen = :propriete)';
            $query = $db->prepare($sql);
            $query->execute(['colis' => $id, 'propriete' => $id]);
            if ((int) $query->fetchColumn() > 0) {
                $db->rollBack();
                return $this->resultat(['general' => 'Ce compte possède des colis ou des propriétés. Désactivez-le pour conserver leur historique.']);
            }
            (new AuditController($db))->log($_SESSION['user_id'] ?? null, 'USER_DELETED', 'Compte #' . $id . ' supprimé.', $id);
            $sql = 'DELETE FROM citoyen WHERE id_citoyen = :id';
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $db->commit();
            return ['erreurs' => [], 'utilisateur' => null, 'id' => (int) $id];
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
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
            $sql = 'SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, mot_de_passe, role, actif, version_session, created_at
                 FROM citoyen WHERE email = :email LIMIT 1';
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

    public function enregistrer($id, $nom, $email, $password, $confirmation, $role, $actif, $versionAttendue = null, $prenom = null, $telephone = null) {
        if (!in_array($actif, [0, 1, '0', '1'], true)) return $this->resultat(['actif' => 'Choisissez un état de compte valide.']);
        $citoyen = new Citoyen($id, $this->texte($nom), $this->texte($email), is_string($password) ? $password : '',
            is_string($role) ? $role : null, (int) $actif, $prenom, $telephone, $versionAttendue);
        $confirmation = is_string($confirmation) ? $confirmation : '';
        return $id === null ? $this->ajouterCitoyen($citoyen, $confirmation) : $this->modifierCitoyen($citoyen, $id, $confirmation);
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

    public function supprimer($id) {
        return $this->supprimerCitoyen($id);
    }

    private function validerCompte($id, $nom, $email, $password, $confirmation)
    {
        $db = Database::getConnection();
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
            $sql = 'SELECT id_citoyen FROM citoyen WHERE email = :email AND id_citoyen <> :id LIMIT 1';
            $query = $db->prepare($sql);
            $query->execute(['email' => $email, 'id' => (int) $id]);
            if ($query->fetch()) {
                $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
            }
            return $erreurs;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function lireCompte($id, $verrouiller = false)
    {
        $db = Database::getConnection();
        try {
            $sql = 'SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, mot_de_passe, role, actif, version_session, created_at
                    FROM citoyen WHERE id_citoyen = :id';
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

    private function verrouillerAdministrateurs()
    {
        $db = Database::getConnection();
        try {
            $sql = "SELECT id_citoyen FROM citoyen WHERE role = 'admin' AND actif = 1 ORDER BY id_citoyen FOR UPDATE";
            $query = $db->query($sql);
            return $query->fetchAll();
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
        $config = self::captchaConfiguration();
        if ($config['site_key'] === '' || $config['secret_key'] === '') {
            return $config['environment'] === 'development';
        }
        $token = posteText($_POST, 'g-recaptcha-response');
        if ($token === '') return false;
        $url = 'https://www.google.com/recaptcha/api/siteverify'
             . '?secret=' . urlencode($config['secret_key']) . '&response=' . urlencode($token);
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
            if (!$this->verifierCaptcha()) $erreurs['captcha'] = self::captchaEnabled()
                ? 'Veuillez cocher la case « Je ne suis pas un robot ». '
                : 'La vérification de sécurité n’est pas configurée pour cet environnement.';
            if (count($erreurs) == 0) {
                $citoyen = new Citoyen(null, $old['nom'], $old['email'], $old['password'], 'citoyen', 1, $old['prenom'], $old['telephone'], null);
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
            if (!$this->verifierCaptcha()) $erreurs['captcha'] = self::captchaEnabled()
                ? 'Veuillez cocher la case « Je ne suis pas un robot ». '
                : 'La vérification de sécurité n’est pas configurée pour cet environnement.';
            if (count($erreurs) == 0) {
                $citoyen = $this->verifyLogin($old['email'], $old['password']);
                if ($citoyen) {
                    connecterUtilisateur($citoyen);
                    $destination = $citoyen['role'] === 'admin' ? 'dashboard'
                        : ($citoyen['role'] === 'agent' ? 'admin-demandes-cnam' : 'home');
                    header('Location: index.php?page=' . $destination, true, 303);
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

    // ==============================================================
    // BACK OFFICE : le Controller traite, puis la vue affiche
    // ==============================================================

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
            if (count($erreurs) == 0) {
                $citoyen = new Citoyen(null, $old['nom'], $old['email'], $old['password'], $old['role'], (int) $old['actif'], $old['prenom'], $old['telephone'], null);
                $resultat = $this->ajouterCitoyen($citoyen, $old['confirmation']);
                $erreurs = $resultat['erreurs'];
                if (count($erreurs) == 0) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été ajouté.'];
                    header('Location: index.php?page=admin-utilisateurs', true, 303);
                    exit;
                }
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
            if (count($erreurs) == 0) {
                // Si l'admin modifie son propre compte, on vérifie que sa session est toujours valable.
                $version = $id === $compteConnecte['id'] ? $compteConnecte['version_session'] : null;
                $citoyen = new Citoyen($id, $old['nom'], $old['email'], $old['password'], $old['role'], (int) $old['actif'], $old['prenom'], $old['telephone'], $version);
                $resultat = $this->modifierCitoyen($citoyen, $id, $old['confirmation']);
                $erreurs = $resultat['erreurs'];
                if (count($erreurs) == 0) {
                    if ($id === $compteConnecte['id']) {
                        $compte = $resultat['utilisateur'];
                        if (!$compte || $compte['actif'] !== 1 || $compte['role'] !== 'admin') {
                            deconnecterUtilisateur();
                            header('Location: ' . frontUrl('connexion'), true, 303);
                            exit;
                        }
                        connecterUtilisateur($compte);
                    }
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été mis à jour.'];
                    header('Location: index.php?page=admin-utilisateurs', true, 303);
                    exit;
                }
            }
            http_response_code(422);
        }

        $this->renderBack('utilisateurs/updateUser', [
            'title' => 'Modifier un utilisateur', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
            'id' => $id, 'old' => $old, 'erreurs' => $erreurs, 'csrfToken' => csrfToken(),
        ]);
    }

    // Pas de vue : on supprime puis on revient à la liste avec un message.
    public function deleteBack(): void {
        self::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {
            $id = posteId($_POST);
            $resultat = $id === null ? ['erreurs' => ['general' => 'Identifiant utilisateur invalide.']] : $this->supprimerCitoyen($id);
            if (count($resultat['erreurs']) == 0) {
                if ($id === (int) $_SESSION['user_id']) {
                    deconnecterUtilisateur();
                    header('Location: ' . frontUrl('connexion'), true, 303);
                    exit;
                }
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été supprimé.'];
            } else {
                $_SESSION['flash'] = ['type' => 'warning', 'message' => implode(' ', $resultat['erreurs'])];
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
        }
        header('Location: index.php?page=admin-utilisateurs', true, 303);
        exit;
    }
}
