<?php

require_once __DIR__ . '/../../config.php';

class AuditLog {
    public const ACTIONS = [
        'LOGIN' => 'Connexion réussie',
        'LOGIN_FAILED' => 'Connexion refusée',
        'LOGOUT' => 'Déconnexion',
        'USER_CREATED' => 'Création d’un utilisateur',
        'USER_UPDATED' => 'Modification d’un utilisateur',
        'USER_DELETED' => 'Suppression d’un utilisateur',
        'ROLE_CHANGED' => 'Changement de rôle',
        'ACCOUNT_SUSPENDED' => 'Suspension du compte',
        'ACCOUNT_ACTIVATED' => 'Activation du compte',
        'ACCESS_DENIED' => 'Accès refusé',
    ];

    private PDO $db;

    public function __construct($connexion = null) {
        $this->db = $connexion ?? Database::getConnection();
    }

    public function log($userId, string $action, string $details, $targetId = null): void {
        if (!isset(self::ACTIONS[$action])) throw new InvalidArgumentException('Action d’audit inconnue.');
        $userId = filter_var($userId, FILTER_VALIDATE_INT);
        $userId = $userId !== false && $userId > 0 ? $userId : null;
        $targetId = filter_var($targetId, FILTER_VALIDATE_INT);
        $targetId = $targetId !== false && $targetId > 0 ? $targetId : null;
        $nom = 'Visiteur';
        if ($userId !== null) {
            $requete = $this->db->prepare('SELECT nom, prenom FROM citoyen WHERE id_citoyen = :id');
            $requete->execute(['id' => $userId]);
            $compte = $requete->fetch();
            if ($compte) $nom = trim($compte['prenom'] . ' ' . $compte['nom']);
            else {
                if ((int) ($_SESSION['user_id'] ?? 0) === $userId) {
                    $nom = trim(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? 'Compte supprimé'));
                }
                $userId = null;
            }
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ip = is_string($ip) && filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
        $requete = $this->db->prepare(
            'INSERT INTO audit_logs (user_id, actor_name, action, target_id, details, ip_address)
             VALUES (:user_id, :actor_name, :action, :target_id, :details, :ip)'
        );
        $requete->execute(['user_id' => $userId, 'actor_name' => mb_substr($nom, 0, 161, 'UTF-8'),
            'action' => $action, 'target_id' => $targetId, 'details' => mb_substr($details, 0, 500, 'UTF-8'), 'ip' => $ip]);
    }

    private function conditions(array $filtres): array {
        $conditions = [];
        $parametres = [];
        if ($filtres['utilisateur'] !== null) {
            $conditions[] = '(user_id = :acteur OR target_id = :cible)';
            $parametres['acteur'] = $filtres['utilisateur'];
            $parametres['cible'] = $filtres['utilisateur'];
        }
        if ($filtres['action'] !== '') {
            $conditions[] = 'action = :action';
            $parametres['action'] = $filtres['action'];
        }
        if ($filtres['debut'] !== '') {
            $conditions[] = 'created_at >= :debut';
            $parametres['debut'] = $filtres['debut'] . ' 00:00:00';
        }
        if ($filtres['fin'] !== '') {
            $conditions[] = 'created_at <= :fin';
            $parametres['fin'] = $filtres['fin'] . ' 23:59:59';
        }
        return [$conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions), $parametres];
    }

    public function compter(array $filtres): int {
        [$where, $parametres] = $this->conditions($filtres);
        $requete = $this->db->prepare('SELECT COUNT(*) FROM audit_logs' . $where);
        $requete->execute($parametres);
        return (int) $requete->fetchColumn();
    }

    public function lister(array $filtres, int $numeroPage, int $taille = 25): array {
        [$where, $parametres] = $this->conditions($filtres);
        $requete = $this->db->prepare('SELECT * FROM audit_logs' . $where . ' ORDER BY created_at DESC, id DESC LIMIT :taille OFFSET :decalage');
        foreach ($parametres as $nom => $valeur) $requete->bindValue(':' . $nom, $valeur);
        $requete->bindValue(':taille', $taille, PDO::PARAM_INT);
        $requete->bindValue(':decalage', ($numeroPage - 1) * $taille, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll();
    }

    public function utilisateurs(): array {
        return $this->db->query('SELECT id_citoyen, nom, prenom FROM citoyen ORDER BY nom, prenom')->fetchAll();
    }
}
