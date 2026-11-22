<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/poste.php';
require_once __DIR__ . '/app/security.php';
require_once __DIR__ . '/app/i18n.php';
if (isset($_GET['lang']) && is_string($_GET['lang']) && in_array($_GET['lang'], ET_LANGS, true)) $_SESSION['lang'] = $_GET['lang'];
utilisateurConnecte();
require_once __DIR__ . '/app/Controllers/Controller.php';
require_once __DIR__ . '/app/Controllers/CitoyenController.php';
require_once __DIR__ . '/app/Controllers/HomeController.php';
require_once __DIR__ . '/app/Controllers/PagesController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/routes.php';

$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home';
if (($_GET['espace'] ?? '') === 'admin') {
    $anciens = ['bureaux' => 'admin-bureaux', 'bureauList' => 'admin-bureaux', 'bureauliste' => 'admin-bureaux',
        'colis' => 'admin-colis', 'colisList' => 'admin-colis', 'Colisliste' => 'admin-colis', 'searchColis' => 'admin-colis',
        'bureau-form' => 'admin-bureau-form', 'bureau-save' => 'admin-bureau-save', 'bureau-suppression' => 'admin-bureau-delete',
        'bureau-delete' => 'deleteBureau', 'colis-form' => 'admin-colis-form', 'colis-save' => 'admin-colis-save',
        'colis-suppression' => 'admin-colis-delete', 'colis-delete' => 'deleteColis',
        'utilisateurs' => 'admin-utilisateurs', 'userList' => 'admin-utilisateurs', 'utilisateur-form' => 'admin-utilisateur-form',
        'utilisateur-save' => 'admin-utilisateur-save', 'utilisateur-suppression' => 'admin-utilisateur-delete', 'utilisateur-delete' => 'deleteUser'];
    $page = $anciens[$page] ?? $page;
}
if (!isset($routes[$page])) {
    http_response_code(404);
    (new Controller())->renderFront('404', ['title' => 'Page introuvable', 'currentPage' => '404']);
    exit;
}
$route = $routes[$page];
$access = $route[3] ?? null;
if ($access === 'admin') {
    CitoyenController::requireAdmin();
} elseif (is_array($access)) {
    exigerRole($access);
} elseif (strpos($page, 'admin-') === 0 || $page === 'dashboard') {
    CitoyenController::requireAdmin();
}
$methodes = $route[2] ?? ['GET','POST'];
if (!in_array($_SERVER['REQUEST_METHOD'], $methodes, true)) {
    http_response_code(405);
    header('Allow: ' . implode(', ', $methodes));
    exit('Méthode non autorisée.');
}
// Les scripts Delete vérifient leur jeton avant de rediriger.
$suppressionAdmin = in_array($route[0], ['BureauPostalController', 'ColisController', 'CitoyenController'], true)
    && $route[1] === 'deleteBack';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$suppressionAdmin && !csrfValide($_POST['csrf'] ?? '')) {
    auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
    if ($page === 'login' || $page === 'connexion') auditEvenement('LOGIN_FAILED', 'Jeton CSRF invalide.');
    http_response_code(403);
    exit('Le formulaire a expiré. Rechargez la page avant de réessayer.');
}
if (in_array($page, ['addBureau','addColis','addUser','updateBureau','updateColis','updateUser'], true)) {
    $valeurs = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
    if (strpos($page, 'add') === 0 && !empty($valeurs['id'])) {
        http_response_code(400); exit('Un ajout ne reçoit pas un identifiant existant.');
    }
    if (strpos($page, 'update') === 0 && (!isset($valeurs['id']) || !is_string($valeurs['id']) || filter_var($valeurs['id'], FILTER_VALIDATE_INT) <= 0)) {
        http_response_code(404); exit('Choisissez un élément dans la liste avant de le modifier.');
    }
}
$controller = new $route[0]();
$controller->{$route[1]}();
