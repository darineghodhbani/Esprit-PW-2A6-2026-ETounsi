<?php
session_start();
require_once __DIR__ . '/app/i18n.php';

require_once __DIR__ . '/app/Controllers/CitoyenController.php';
require_once __DIR__ . '/app/Controllers/Controller.php';
require_once __DIR__ . '/app/Controllers/HomeController.php';
require_once __DIR__ . '/app/Controllers/PagesController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/routes.php';

$page = $_GET['page'] ?? 'home';

if (!isset($routes[$page])) {
    http_response_code(404);
    echo 'Page introuvable.';
    exit;
}

[$controllerName, $methode] = $routes[$page];
$controller = new $controllerName();
$controller->$methode();
