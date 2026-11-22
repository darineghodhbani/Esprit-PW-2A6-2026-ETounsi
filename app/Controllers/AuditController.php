<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/AuditLog.php';

class AuditController extends Controller {
    public const ACTIONS = AuditLog::ACTIONS;

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

    public static function clean(array $get): array {
        return [
            'utilisateur' => posteId($get, 'utilisateur'),
            'action' => posteText($get, 'action'),
            'debut' => posteText($get, 'debut'),
            'fin' => posteText($get, 'fin'),
        ];
    }

    public function verifierFiltres(array $d): array {
        $erreurs = [];
        if ($d['action'] !== '' && !isset(AuditLog::ACTIONS[$d['action']])) $erreurs['action'] = 'Choisissez une action dans la liste.';
        foreach (['debut', 'fin'] as $champ) {
            $brute = $_GET[$champ] ?? '';
            if (!is_string($brute) || preg_match('/[\x00-\x1F]/', $brute)) {
                $erreurs[$champ] = 'Saisissez une date valide.';
                continue;
            }
            if ($d[$champ] === '') continue;
            $date = preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $d[$champ]) ? DateTime::createFromFormat('!Y-m-d', $d[$champ]) : false;
            if (!$date || $date->format('Y-m-d') !== $d[$champ]) $erreurs[$champ] = 'Saisissez une date valide.';
        }
        if (!isset($erreurs['debut']) && !isset($erreurs['fin']) && $d['debut'] !== '' && $d['fin'] !== '' && $d['debut'] > $d['fin']) {
            $erreurs['dates'] = 'La date de début doit précéder la date de fin.';
        }
        return $erreurs;
    }

    public function listBack(): void {
        CitoyenController::requireAdmin();
        $old = self::clean($_GET);
        $erreurs = $this->verifierFiltres($old);
        if (count($erreurs) > 0) {
            http_response_code(422);
            foreach (['action', 'debut', 'fin'] as $champ) {
                if (isset($erreurs[$champ])) $old[$champ] = '';
            }
        }
        $total = $this->compter($old);
        $nombrePages = max(1, (int) ceil($total / 25));
        $numero = min($nombrePages, posteId($_GET, 'numero') ?? 1);
        $utilisateurs = $this->utilisateurs();
        if ($old['utilisateur'] !== null && !in_array($old['utilisateur'], array_column($utilisateurs, 'id_citoyen'))) {
            $utilisateurs[] = ['id_citoyen' => $old['utilisateur'], 'prenom' => '', 'nom' => 'Compte supprimé #' . $old['utilisateur']];
        }
        $this->renderBack('audit/index', [
            'title' => 'Journal d’audit — E-Tounsi', 'currentPage' => 'admin-audit',
            'lignes' => $this->lister($old, $numero), 'filtres' => $old,
            'actions' => AuditLog::ACTIONS, 'utilisateurs' => $utilisateurs, 'erreurs' => $erreurs,
            'total' => $total, 'numero' => $numero, 'nombrePages' => $nombrePages,
        ]);
    }
}
