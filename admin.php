<?php

declare(strict_types=1);

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/Controllers/AdminController.php';

$auth = new AuthController();
$auth->refreshSession();

$controller = new AdminController();
$page = isset($_GET['page']) ? (string) $_GET['page'] : 'dashboard';

switch ($page) {
    case 'proprietes':       $controller->proprietes(); break;
    case 'propriete-form':   $controller->proprieteForm(); break;
    case 'propriete-delete': $controller->proprieteDelete(); break;

    case 'impots':           $controller->impots(); break;
    case 'impot-form':       $controller->impotForm(); break;
    case 'impot-delete':     $controller->impotDelete(); break;

    case 'users':           $controller->users(); break;
    case 'user-show':       $controller->userShow(); break;
    case 'user-form':       $controller->userForm(); break;
    case 'user-toggle':     $controller->userToggle(); break;
    case 'user-delete':     $controller->userDelete(); break;

    default:                $controller->dashboard(); break;
}
