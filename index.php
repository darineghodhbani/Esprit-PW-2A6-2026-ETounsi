<?php
// Point d'entrée du site : toutes les pages passent par ici.
// Exemple : index.php?page=about  → affiche la page "À propos"
require_once __DIR__ . '/Controllers/HomeController.php';

$controller = new HomeController();
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'home':        $controller->home(); break;
    case 'about':       $controller->about(); break;
    case 'courses':     $controller->courses(); break;
    case 'team':        $controller->team(); break;
    case 'testimonial': $controller->testimonial(); break;
    case 'contact':     $controller->contact(); break;
    default:            $controller->notFound(); break;
}
