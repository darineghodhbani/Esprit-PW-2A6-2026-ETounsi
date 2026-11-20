<?php

declare(strict_types=1);

require_once __DIR__ . '/config/session.php';

require_once __DIR__ . '/Controllers/HomeController.php';

$auth = new AuthController();
$auth->refreshSession();

$controller = new HomeController($auth);
$page = isset($_GET['page']) ? (string) $_GET['page'] : 'home';

switch ($page) {
    case 'home':          $controller->home(); break;
    case 'administratif': $controller->administratif(); break;

    case 'login':         $controller->login(); break;
    case 'register':      $controller->register(); break;
    case 'logout':        $controller->logout(); break;
    case 'profil':        $controller->profil(); break;

    default:              $controller->notFound(); break;
}
