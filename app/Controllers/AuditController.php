<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/AuditLog.php';

class AuditController extends Controller {
    public function index(): void {
        CitoyenController::requireAdmin();
        $filtres = ['utilisateur' => posteId($_GET, 'utilisateur'), 'action' => posteText($_GET, 'action'),
            'debut' => posteText($_GET, 'debut'), 'fin' => posteText($_GET, 'fin')];
        $erreurs = [];
        if ($filtres['action'] !== '' && !isset(AuditLog::ACTIONS[$filtres['action']])) {
            $erreurs[] = 'Choisissez une action dans la liste.';
            $filtres['action'] = '';
        }
        foreach (['debut', 'fin'] as $champ) {
            $valeurBrute = $_GET[$champ] ?? '';
            if (!is_string($valeurBrute) || preg_match('/[\x00-\x1F]/', $valeurBrute)) {
                $erreurs[] = 'Saisissez une date valide.';
                $filtres[$champ] = '';
                continue;
            }
            if ($filtres[$champ] === '') continue;
            $date = preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $filtres[$champ])
                ? DateTime::createFromFormat('!Y-m-d', $filtres[$champ]) : false;
            if (!$date || $date->format('Y-m-d') !== $filtres[$champ]) {
                $erreurs[] = 'Saisissez une date valide.';
                $filtres[$champ] = '';
            }
        }
        if ($filtres['debut'] !== '' && $filtres['fin'] !== '' && $filtres['debut'] > $filtres['fin']) {
            $erreurs[] = 'La date de début doit précéder la date de fin.';
        }
        if ($erreurs !== []) http_response_code(422);
        $modele = new AuditLog();
        $total = $modele->compter($filtres);
        $nombrePages = max(1, (int) ceil($total / 25));
        $numero = min($nombrePages, posteId($_GET, 'numero') ?? 1);
        $utilisateurs = $modele->utilisateurs();
        $ids = array_column($utilisateurs, 'id_citoyen');
        if ($filtres['utilisateur'] !== null && !in_array($filtres['utilisateur'], $ids)) {
            $utilisateurs[] = ['id_citoyen' => $filtres['utilisateur'], 'prenom' => '', 'nom' => 'Compte supprimé #' . $filtres['utilisateur']];
        }
        $this->renderBack('audit/index', ['title' => 'Journal d’audit — E-Tounsi', 'currentPage' => 'admin-audit',
            'lignes' => $modele->lister($filtres, $numero), 'filtres' => $filtres, 'actions' => AuditLog::ACTIONS,
            'utilisateurs' => $utilisateurs, 'erreurs' => $erreurs, 'total' => $total, 'numero' => $numero, 'nombrePages' => $nombrePages]);
    }
}
