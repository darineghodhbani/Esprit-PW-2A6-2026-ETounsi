<?php
// Point d'entrée du site : toutes les pages passent par ici.
// Exemple : index.php?page=about  → affiche la page "À propos"
define('ETOUNSI_PORTAIL', true);
require_once __DIR__ . '/modules/poste/app/views/shared/portail.php';
require_once __DIR__ . '/Controllers/HomeController.php';

$controller = new HomeController();
$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home';

// Le portail partage la vraie session Poste sans charger son routeur MVC.
if ($page === 'home' || $page === 'service') {
    require_once __DIR__ . '/modules/poste/config/database.php';
    require_once __DIR__ . '/modules/poste/app/core/fonctions.php';
    require_once __DIR__ . '/modules/poste/app/core/securite.php';
}

switch ($page) {
    case 'home':        $controller->home(); break;
    case 'service':     $controller->service(); break;
    case 'about':       $controller->about(); break;
    case 'courses':     $controller->courses(); break;
    case 'team':        $controller->team(); break;
    case 'testimonial': $controller->testimonial(); break;
    case 'contact':     $controller->contact(); break;
    default:            $controller->notFound(); break;
}
