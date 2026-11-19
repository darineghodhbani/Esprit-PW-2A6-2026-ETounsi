<?php

require_once __DIR__ . '/../models/Utilisateur.php';

class AuthController
{
    const ROLES = ['citoyen', 'admin_poste', 'super_admin'];

    private $connexion;

    public function __construct($connexion)
    {
        $this->connexion = $connexion;
    }

    public function lister()
    {
        $requete = $this->connexion->query(
            'SELECT id_citoyen AS id, nom, email, role, actif, version_session, created_at
             FROM Citoyen ORDER BY nom, id_citoyen'
        );
        $utilisateurs = [];
        foreach ($requete->fetchAll() as $ligne) {
            $utilisateurs[] = Utilisateur::depuisTableau($ligne)->versTableau();
        }
        return $utilisateurs;
    }

    public function trouver($id)
    {
        $ligne = $this->lireCompte($id);
        if ($ligne === false) {
            return null;
        }
        return Utilisateur::depuisTableau($ligne)->versTableau();
    }

    public function inscrire($nom, $email, $password, $confirmation)
    {
        return $this->enregistrer(null, $nom, $email, $password, $confirmation, 'citoyen', 1);
    }

    public function enregistrer($id, $nom, $email, $password, $confirmation, $role, $actif, $versionAttendue = null)
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

        try {
            $this->connexion->beginTransaction();
            // Ces lignes restent verrouillées jusqu'à la fin de la modification.
            $administrateurs = $this->verrouillerAdministrateurs();
            if ($id === null) {
                $requete = $this->connexion->prepare(
                    'INSERT INTO Citoyen (nom, email, mot_de_passe, role, actif)
                     VALUES (:nom, :email, :mot_de_passe, :role, :actif)'
                );
                $requete->execute([
                    'nom' => $nom,
                    'email' => $email,
                    'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'actif' => (int) $actif,
                ]);
                $id = (int) $this->connexion->lastInsertId();
            } else {
                $compte = $this->lireCompte($id, true);
                if ($compte === false) {
                    $this->connexion->rollBack();
                    return $this->resultat(['general' => 'Cet utilisateur n’existe plus.']);
                }
                if ($versionAttendue !== null && (int) $compte['version_session'] !== (int) $versionAttendue) {
                    $this->connexion->rollBack();
                    return $this->resultat(['general' => 'Votre compte a été modifié. Reconnectez-vous avant de modifier votre profil.']);
                }
                if ($compte['role'] === 'super_admin' && (int) $compte['actif'] === 1
                    && ($role !== 'super_admin' || (int) $actif !== 1) && count($administrateurs) <= 1) {
                    $this->connexion->rollBack();
                    return $this->resultat(['general' => 'Conservez au moins un super administrateur actif.']);
                }
                $motDePasse = $compte['mot_de_passe'];
                $version = (int) $compte['version_session'];
                if ($password !== '') {
                    $motDePasse = password_hash($password, PASSWORD_DEFAULT);
                }
                if ($password !== '' || $role !== $compte['role'] || (int) $actif !== (int) $compte['actif']) {
                    $version++;
                }
                $requete = $this->connexion->prepare(
                    'UPDATE Citoyen SET nom = :nom, email = :email, mot_de_passe = :mot_de_passe,
                     role = :role, actif = :actif, version_session = :version WHERE id_citoyen = :id'
                );
                $requete->execute([
                    'nom' => $nom, 'email' => $email, 'mot_de_passe' => $motDePasse,
                    'role' => $role, 'actif' => (int) $actif, 'version' => $version, 'id' => $id,
                ]);
            }
            $utilisateur = $this->trouver($id);
            $this->connexion->commit();
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
            $this->connexion->beginTransaction();
            $compte = $this->lireCompte($id, true);
            if ($compte === false || (int) $compte['actif'] !== 1) {
                $this->connexion->rollBack();
                return $this->resultat(['general' => 'Ce compte est indisponible.']);
            }
            // La version provient de la session vérifiée avant le traitement du formulaire.
            if ((int) $compte['version_session'] !== (int) $versionAttendue) {
                $this->connexion->rollBack();
                return $this->resultat(['general' => 'Votre compte a été modifié. Reconnectez-vous avant de modifier votre profil.']);
            }
            $identifiantsModifies = $password !== '' || $email !== $compte['email'];
            $motDePasseActuelValide = strlen($motDePasseActuel) <= 72 && strpos($motDePasseActuel, "\0") === false;
            if ($identifiantsModifies && (!$motDePasseActuelValide || !password_verify($motDePasseActuel, $compte['mot_de_passe'] ?? ''))) {
                $this->connexion->rollBack();
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
            $requete = $this->connexion->prepare(
                'UPDATE Citoyen SET nom = :nom, email = :email, mot_de_passe = :mot_de_passe,
                 version_session = :version WHERE id_citoyen = :id'
            );
            $requete->execute([
                'nom' => $nom, 'email' => $email, 'mot_de_passe' => $motDePasse,
                'version' => $version, 'id' => $id,
            ]);
            $utilisateur = $this->trouver($id);
            $this->connexion->commit();
            return $this->resultat([], $utilisateur);
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
    }

    public function supprimer($id)
    {
        try {
            $this->connexion->beginTransaction();
            $administrateurs = $this->verrouillerAdministrateurs();
            $compte = $this->lireCompte($id, true);
            if ($compte === false) {
                $this->connexion->rollBack();
                return $this->resultat(['general' => 'Cet utilisateur n’existe plus.']);
            }
            if ($compte['role'] === 'super_admin' && (int) $compte['actif'] === 1 && count($administrateurs) <= 1) {
                $this->connexion->rollBack();
                return $this->resultat(['general' => 'Conservez au moins un super administrateur actif.']);
            }
            $requete = $this->connexion->prepare('SELECT COUNT(*) FROM Colis WHERE id_citoyen = :id');
            $requete->execute(['id' => $id]);
            if ((int) $requete->fetchColumn() > 0) {
                $this->connexion->rollBack();
                return $this->resultat(['general' => 'Ce compte possède des colis. Désactivez-le pour conserver leur historique.']);
            }
            $requete = $this->connexion->prepare('DELETE FROM Citoyen WHERE id_citoyen = :id');
            $requete->execute(['id' => $id]);
            $this->connexion->commit();
            return ['erreurs' => [], 'utilisateur' => null, 'id' => (int) $id];
        } catch (PDOException $erreur) {
            return $this->erreurBase($erreur);
        }
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
            $requete = $this->connexion->prepare(
                'SELECT id_citoyen AS id, nom, email, mot_de_passe, role, actif, version_session, created_at
                 FROM Citoyen WHERE email = :email LIMIT 1'
            );
            $requete->execute(['email' => $email]);
            $compte = $requete->fetch();
        }
        if (!$compte || (int) $compte['actif'] !== 1 || !password_verify($password, $compte['mot_de_passe'] ?? '')) {
            $_SESSION['tentatives_connexion'][] = $maintenant;
            return $this->resultat(['general' => 'Adresse email ou mot de passe incorrect.']);
        }
        unset($_SESSION['tentatives_connexion']);
        return $this->resultat([], Utilisateur::depuisTableau($compte)->versTableau());
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
        $requete = $this->connexion->prepare(
            'SELECT id_citoyen FROM Citoyen WHERE email = :email AND id_citoyen <> :id LIMIT 1'
        );
        $requete->execute(['email' => $email, 'id' => (int) $id]);
        if ($requete->fetch()) {
            $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
        }
        return $erreurs;
    }

    private function lireCompte($id, $verrouiller = false)
    {
        $sql = 'SELECT id_citoyen AS id, nom, email, mot_de_passe, role, actif, version_session, created_at
                FROM Citoyen WHERE id_citoyen = :id';
        if ($verrouiller) {
            $sql .= ' FOR UPDATE';
        }
        $requete = $this->connexion->prepare($sql);
        $requete->execute(['id' => $id]);
        return $requete->fetch();
    }

    private function verrouillerAdministrateurs()
    {
        $requete = $this->connexion->query(
            "SELECT id_citoyen FROM Citoyen WHERE role = 'super_admin' AND actif = 1 ORDER BY id_citoyen FOR UPDATE"
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
        if ($this->connexion->inTransaction()) {
            $this->connexion->rollBack();
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
