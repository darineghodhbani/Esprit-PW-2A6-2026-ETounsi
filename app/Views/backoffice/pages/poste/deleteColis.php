<?php
require_once __DIR__ . '/../../../../Controllers/CitoyenController.php';
require_once __DIR__ . '/../../../../Controllers/ColisController.php';
CitoyenController::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {
    $colisC = new ColisController();
    $id = posteId($_POST);
    $supprime = $id !== null && $colisC->supprimerColis($id);
    $_SESSION['flash'] = [
        'type' => $supprime ? 'success' : 'warning',
        'message' => $supprime ? 'Le colis a été supprimé.' : 'Colis introuvable ou identifiant invalide.',
    ];
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
}

header('Location: index.php?page=admin-colis', true, 303);
exit;
