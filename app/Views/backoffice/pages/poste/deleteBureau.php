<?php
require_once __DIR__ . '/../../../../Controllers/CitoyenController.php';
require_once __DIR__ . '/../../../../Controllers/BureauPostalController.php';
CitoyenController::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {
    $bureauC = new BureauPostalController();
    $id = posteId($_POST);
    $supprime = $id !== null && $bureauC->supprimerBureau($id);
    $_SESSION['flash'] = [
        'type' => $supprime ? 'success' : 'warning',
        'message' => $supprime ? 'Le bureau a été supprimé.' : 'Bureau introuvable ou associé à des colis : suppression refusée.',
    ];
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
}

header('Location: index.php?page=admin-bureaux', true, 303);
exit;
