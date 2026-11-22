<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/AuditLog.php';

class Citoyen {
    public const ROLES = ['citoyen', 'admin'];
    private PDO $db;

    public function __construct($connexion = null) {
        $this->db = $connexion ?? Database::getConnection();
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
        $requete = $this->db->prepare('SELECT id_citoyen FROM citoyen WHERE email = :email');
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
            $requete = $this->db->prepare(
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
            $this->db->beginTransaction();
            $compteVerifie = $this->lireCompte($compte['id'], true);
            if (!$compteVerifie || (int) $compteVerifie['actif'] !== 1
                || $compteVerifie['version_session'] !== $compte['version_session']
                || $compteVerifie['mot_de_passe'] !== $compte['mot_de_passe']) {
                $this->db->rollBack();
                return $this->resultat(['general' => 'Le compte a changé. Reconnectez-vous.']);
            }
            $utilisateur = $this->trouver($compte['id']);
            (new AuditLog($this->db))->log($compte['id'], 'LOGIN', 'Connexion réussie.', $compte['id']);
            $this->db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    public function lister()
    {
        $requete = $this->db->query(
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
        $nom = $this->texte($nom);
        $email = strtolower($this->texte($email));
        $password = is_string($password) ? $password : '';
        $confirmation = is_string($confirmation) ? $confirmation : '';
        $erreurs = $this->validerCompte($id, $nom, $email, $password, $confirmation);
        if (!in_array($role, self::ROLES, true)) {
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
            $this->db->beginTransaction();
            // Ces lignes restent verrouillées jusqu'à la fin de la modification.
            $administrateurs = $this->verrouillerAdministrateurs();
            if ($id === null) {
                $requete = $this->db->prepare(
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
                $id = (int) $this->db->lastInsertId();
            } else {
                $compte = $this->lireCompte($id, true);
                if ($compte === false) {
                    $this->db->rollBack();
                    return $this->resultat(['general' => 'Cet utilisateur n’existe plus.']);
                }
                if ($versionAttendue !== null && (int) $compte['version_session'] !== (int) $versionAttendue) {
                    $this->db->rollBack();
                    return $this->resultat(['general' => 'Votre compte a été modifié. Reconnectez-vous avant de modifier votre profil.']);
                }
                if ($compte['role'] === 'admin' && (int) $compte['actif'] === 1
                    && ($role !== 'admin' || (int) $actif !== 1) && count($administrateurs) <= 1) {
                    $this->db->rollBack();
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
                $requete = $this->db->prepare(
                    'UPDATE citoyen SET nom = :nom, email = :email, mot_de_passe = :mot_de_passe,
                     role = :role, actif = :actif, version_session = :version WHERE id_citoyen = :id'
                );
                $requete->execute([
                    'nom' => $nom, 'email' => $email, 'mot_de_passe' => $motDePasse,
                    'role' => $role, 'actif' => (int) $actif, 'version' => $version, 'id' => $id,
                ]);
            }
            $utilisateur = $this->trouver($id);
            $audit = new AuditLog($this->db);
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
            $this->db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    public function modifierProfil($id, $nom, $email, $password, $confirmation, $motDePasseActuel, $versionAttendue)
    {
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
            $this->db->beginTransaction();
            $compte = $this->lireCompte($id, true);
            if ($compte === false || (int) $compte['actif'] !== 1) {
                $this->db->rollBack();
                return $this->resultat(['general' => 'Ce compte est indisponible.']);
            }
            // La version provient de la session vérifiée avant le traitement du formulaire.
            if ((int) $compte['version_session'] !== (int) $versionAttendue) {
                $this->db->rollBack();
                return $this->resultat(['general' => 'Votre compte a été modifié. Reconnectez-vous avant de modifier votre profil.']);
            }
            $identifiantsModifies = $password !== '' || $email !== $compte['email'];
            $motDePasseActuelValide = strlen($motDePasseActuel) <= 72 && strpos($motDePasseActuel, "\0") === false;
            if ($identifiantsModifies && (!$motDePasseActuelValide || !password_verify($motDePasseActuel, $compte['mot_de_passe'] ?? ''))) {
                $this->db->rollBack();
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
            $requete = $this->db->prepare(
                'UPDATE citoyen SET nom = :nom, email = :email, mot_de_passe = :mot_de_passe,
                 version_session = :version WHERE id_citoyen = :id'
            );
            $requete->execute([
                'nom' => $nom, 'email' => $email, 'mot_de_passe' => $motDePasse,
                'version' => $version, 'id' => $id,
            ]);
            $utilisateur = $this->trouver($id);
            (new AuditLog($this->db))->log($_SESSION['user_id'] ?? $id, 'USER_UPDATED', 'Profil mis à jour.', $id);
            $this->db->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    public function supprimer($id)
    {
        try {
            $this->db->beginTransaction();
            $administrateurs = $this->verrouillerAdministrateurs();
            $compte = $this->lireCompte($id, true);
            if ($compte === false) {
                $this->db->rollBack();
                return $this->resultat(['general' => 'Cet utilisateur n’existe plus.']);
            }
            if ($compte['role'] === 'admin' && (int) $compte['actif'] === 1 && count($administrateurs) <= 1) {
                $this->db->rollBack();
                return $this->resultat(['general' => 'Conservez au moins un administrateur actif.']);
            }
            $requete = $this->db->prepare('SELECT (SELECT COUNT(*) FROM colis WHERE id_citoyen = :colis) + (SELECT COUNT(*) FROM propriete WHERE id_citoyen = :propriete)');
            $requete->execute(['colis' => $id, 'propriete' => $id]);
            if ((int) $requete->fetchColumn() > 0) {
                $this->db->rollBack();
                return $this->resultat(['general' => 'Ce compte possède des colis ou des propriétés. Désactivez-le pour conserver leur historique.']);
            }
            (new AuditLog($this->db))->log($_SESSION['user_id'] ?? null, 'USER_DELETED', 'Compte #' . $id . ' supprimé.', $id);
            $requete = $this->db->prepare('DELETE FROM citoyen WHERE id_citoyen = :id');
            $requete->execute(['id' => $id]);
            $this->db->commit();
            return ['erreurs' => [], 'utilisateur' => null, 'id' => (int) $id];
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    private function validerCompte($id, $nom, $email, $password, $confirmation)
    {
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
        $requete = $this->db->prepare(
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
        $sql = 'SELECT id_citoyen AS id, id_citoyen, nom, prenom, telephone, email, mot_de_passe, role, actif, version_session, created_at
                FROM citoyen WHERE id_citoyen = :id';
        if ($verrouiller) {
            $sql .= ' FOR UPDATE';
        }
        $requete = $this->db->prepare($sql);
        $requete->execute(['id' => $id]);
        return $requete->fetch();
    }

    private function verrouillerAdministrateurs()
    {
        $requete = $this->db->query(
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
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
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
}
