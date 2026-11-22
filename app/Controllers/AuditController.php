<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/AuditLog.php';

class AuditController extends Controller {
    private $modele;

    public function __construct() {
        $this->modele = new AuditLog();
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
        $total = $this->modele->compter($old);
        $nombrePages = max(1, (int) ceil($total / 25));
        $numero = min($nombrePages, posteId($_GET, 'numero') ?? 1);
        $utilisateurs = $this->modele->utilisateurs();
        if ($old['utilisateur'] !== null && !in_array($old['utilisateur'], array_column($utilisateurs, 'id_citoyen'))) {
            $utilisateurs[] = ['id_citoyen' => $old['utilisateur'], 'prenom' => '', 'nom' => 'Compte supprimé #' . $old['utilisateur']];
        }
        $this->renderBack('audit/index', [
            'title' => 'Journal d’audit — E-Tounsi', 'currentPage' => 'admin-audit',
            'lignes' => $this->modele->lister($old, $numero), 'filtres' => $old,
            'actions' => AuditLog::ACTIONS, 'utilisateurs' => $utilisateurs, 'erreurs' => $erreurs,
            'total' => $total, 'numero' => $numero, 'nombrePages' => $nombrePages,
        ]);
    }
}
