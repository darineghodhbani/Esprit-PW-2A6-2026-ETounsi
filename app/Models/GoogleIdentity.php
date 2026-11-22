<?php
declare(strict_types=1);
require_once __DIR__ . '/CnamModel.php';
require_once __DIR__ . '/../Controllers/CitoyenController.php';
require_once __DIR__ . '/../Controllers/AuditController.php';

final class GoogleIdentity extends CnamModel {
    public function liee(int $citoyen): bool { return $this->one('SELECT google_sub FROM google_identities WHERE id_citoyen=?',[$citoyen])!==null; }
    /** Receives only claims already verified by GoogleSignIn, never the browser's claims. */
    public function connecter(array $profile, ?array $liaison = null): array {
        try {
            return $this->transaction(function () use ($profile,$liaison) {
                $identity = $this->one('SELECT * FROM google_identities WHERE google_sub=? FOR UPDATE',[$profile['sub']]);
                if ($liaison!==null) {
                    $user = $this->one("SELECT id_citoyen,email,version_session FROM citoyen WHERE id_citoyen=? AND actif=1 AND role='citoyen' FOR UPDATE",[$liaison['id']]);
                    if (!$user || (int)$user['version_session']!==$liaison['version']) throw new DomainException('Votre compte a changé. Reconnectez-vous.',403);
                    if (strtolower($user['email'])!==strtolower($profile['email'])) throw new DomainException('Le compte Google doit utiliser la même adresse que votre compte E-Tounsi.',409);
                    if (($identity && (int)$identity['id_citoyen']!==(int)$user['id_citoyen']) || $this->liee((int)$user['id_citoyen']))
                        throw new DomainException('Ce compte possède déjà une liaison Google.',409);
                    $id = (int)$user['id_citoyen'];
                } elseif ($identity) {
                    $id = (int)$identity['id_citoyen'];
                } else {
                    if ($this->one('SELECT id_citoyen FROM citoyen WHERE email=? FOR UPDATE',[$profile['email']]))
                        throw new DomainException('Cette adresse possède déjà un compte. Connectez-vous avec votre mot de passe puis liez Google depuis votre profil.',409);
                    $nom = mb_substr(trim((string)($profile['family_name']??$profile['name']??'Compte Google')),0,80);
                    $prenom = mb_substr(trim((string)($profile['given_name']??'')),0,80);
                    $this->execute("INSERT INTO citoyen(nom,prenom,email,telephone,mot_de_passe,role,actif) VALUES (?,?,?,'',?,'citoyen',1)",
                        [$nom,$prenom,strtolower($profile['email']),password_hash(bin2hex(random_bytes(32)),PASSWORD_DEFAULT)]);
                    $id = (int)$this->db->lastInsertId();
                    (new AuditController($this->db))->log($id, 'USER_CREATED', 'Compte citoyen créé avec Google.', $id);
                }
                if (!$identity) $this->execute('INSERT INTO google_identities(google_sub,id_citoyen) VALUES (?,?)',[$profile['sub'],$id]);
                $user = (new CitoyenController())->findById($id);
                if (!$user || $user['actif']!==1 || $user['role']!=='citoyen') throw new DomainException('La connexion Google est réservée à un compte citoyen actif.',403);
                (new AuditController($this->db))->log($id, 'LOGIN', 'Connexion citoyenne avec Google.', $id);
                return $user;
            });
        } catch (PDOException $e) {
            if (($e->errorInfo[1]??0)===1062) throw new DomainException('Cette identité ou cette adresse est déjà associée. Reconnectez-vous.',409,$e);
            throw $e;
        }
    }
}
