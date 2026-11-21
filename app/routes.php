<?php

require_once __DIR__ . '/Controllers/ProprieteController.php';
require_once __DIR__ . '/Controllers/ImpotController.php';

$routes = [
    'signup'  => ['CitoyenController', 'signup'],
    'login'   => ['CitoyenController', 'login'],
    'logout'  => ['CitoyenController', 'logout'],
    'profile' => ['CitoyenController', 'profile'],

    'home'      => ['HomeController', 'index'],
    'dashboard' => ['DashboardController', 'index'],

    'administrations' => ['PagesController', 'administrations'],
    'module'          => ['PagesController', 'module'],
    'lang'            => ['PagesController', 'switchLang'],
    'aide'            => ['PagesController', 'aide'],
    'projet'          => ['PagesController', 'projet'],
    'confidentialite' => ['PagesController', 'confidentialite'],

    'declarer-propriete'     => ['ProprieteController', 'declarer'],
    'payer-impot'            => ['ImpotController', 'payer'],
    'mes-recus'              => ['ImpotController', 'recus'],
    'admin-proprietes'       => ['ProprieteController', 'listBack'],
    'admin-propriete-add'    => ['ProprieteController', 'addBack'],
    'admin-propriete-update' => ['ProprieteController', 'updateBack'],
    'admin-propriete-delete' => ['ProprieteController', 'deleteBack'],
    'admin-impots'           => ['ImpotController', 'listBack'],
    'admin-impot-add'        => ['ImpotController', 'addBack'],
    'admin-impot-update'     => ['ImpotController', 'updateBack'],
    'admin-impot-delete'     => ['ImpotController', 'deleteBack'],
];
