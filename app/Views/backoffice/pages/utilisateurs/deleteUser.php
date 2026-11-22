<?php
require_once __DIR__ . '/../../../../Controllers/CitoyenController.php';
CitoyenController::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {
    $citoyenC = new CitoyenController();
    $id = posteId($_POST);
    $resultat = $id === null ? ['erreurs' => ['general' => 'Identifiant utilisateur invalide.']] : $citoyenC->supprimerCitoyen($id);
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
