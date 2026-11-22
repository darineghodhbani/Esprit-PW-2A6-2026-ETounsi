<?php

require_once __DIR__ . '/Controllers/ProprieteController.php';
require_once __DIR__ . '/Controllers/ImpotController.php';

require_once __DIR__ . '/Controllers/BureauPostalController.php';
require_once __DIR__ . '/Controllers/ColisController.php';

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


$routes['bureaux-poste'] = ['BureauPostalController', 'listFront', ['GET']];
$routes['suivi-colis'] = ['ColisController', 'suiviFront', ['GET']];
$routes['declarer-envoi'] = ['ColisController', 'declarerFront', ['GET', 'POST']];
$routes['mes-colis'] = ['ColisController', 'mesColisFront', ['GET', 'POST']];
$routes['modifier-colis'] = ['ColisController', 'modifierFront', ['GET', 'POST']];
$routes['supprimer-colis'] = ['ColisController', 'supprimerFront', ['GET', 'POST']];
$routes['admin-bureaux'] = ['BureauPostalController', 'index', ['GET'], 'admin'];
$routes['admin-bureau-form'] = ['BureauPostalController', 'form', ['GET'], 'admin'];
$routes['admin-bureau-save'] = ['BureauPostalController', 'save', ['POST'], 'admin'];
$routes['admin-bureau-delete'] = ['BureauPostalController', 'confirmDelete', ['GET'], 'admin'];
$routes['admin-colis'] = ['ColisController', 'index', ['GET'], 'admin'];
$routes['admin-colis-form'] = ['ColisController', 'form', ['GET'], 'admin'];
$routes['admin-colis-save'] = ['ColisController', 'save', ['POST'], 'admin'];
$routes['admin-colis-delete'] = ['ColisController', 'confirmDelete', ['GET'], 'admin'];
$routes['admin-utilisateurs'] = ['CitoyenController', 'index', ['GET'], 'admin'];
$routes['admin-utilisateur-form'] = ['CitoyenController', 'form', ['GET'], 'admin'];
$routes['admin-utilisateur-save'] = ['CitoyenController', 'save', ['POST'], 'admin'];
$routes['admin-utilisateur-delete'] = ['CitoyenController', 'confirmDelete', ['GET'], 'admin'];
foreach (['addBureau' => 'BureauPostalController', 'updateBureau' => 'BureauPostalController',
          'addColis' => 'ColisController', 'updateColis' => 'ColisController',
          'addUser' => 'CitoyenController', 'updateUser' => 'CitoyenController'] as $pageForm => $classe) {
    $routes[$pageForm] = [$classe, $_SERVER['REQUEST_METHOD'] === 'POST' ? 'save' : 'form', ['GET','POST'], 'admin'];
}
foreach (['deleteBureau' => 'BureauPostalController', 'deleteColis' => 'ColisController', 'deleteUser' => 'CitoyenController'] as $pageDelete => $classe) {
    $routes[$pageDelete] = [$classe, $_SERVER['REQUEST_METHOD'] === 'POST' ? 'delete' : 'confirmDelete', ['GET','POST'], 'admin'];
}
$routes['colis-accepter'] = ['ColisController', 'accepter', ['POST'], 'admin'];
foreach (['bureaux' => 'bureaux-poste', 'suivi' => 'suivi-colis', 'suiviColis' => 'suivi-colis',
          'envoi' => 'declarer-envoi', 'declarerEnvoi' => 'declarer-envoi', 'historique' => 'mes-colis',
          'mesColis' => 'mes-colis', 'connexion' => 'login', 'inscription' => 'signup', 'profil' => 'profile',
          'register' => 'signup', 'deconnexion' => 'logout'] as $alias => $cible) $routes[$alias] = $routes[$cible];
$routes['logout'][2] = ['POST'];
$routes['deconnexion'][2] = ['POST'];

require_once __DIR__ . '/Controllers/AuditController.php';
$routes['admin-audit'] = ['AuditController', 'index', ['GET'], 'admin'];
