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
$routes['suivi-colis'] = ['ColisController', 'suivi', ['GET']];
$routes['declarer-envoi'] = ['ColisController', 'declarer', ['GET', 'POST']];
$routes['mes-colis'] = ['ColisController', 'mesColis', ['GET', 'POST']];
$routes['modifier-colis'] = ['ColisController', 'modifierFront', ['GET', 'POST']];
$routes['supprimer-colis'] = ['ColisController', 'supprimerFront', ['GET', 'POST']];

$modulesAdmin = [
    'bureau' => ['BureauPostalController', 'admin-bureaux'],
    'colis' => ['ColisController', 'admin-colis'],
    'utilisateur' => ['CitoyenController', 'admin-utilisateurs'],
];
foreach ($modulesAdmin as $entite => $module) {
    $classe = $module[0];
    $routes[$module[1]] = [$classe, 'listBack', ['GET'], 'admin'];
    $routes['admin-' . $entite . '-add'] = [$classe, 'addBack', ['GET', 'POST'], 'admin'];
    $routes['admin-' . $entite . '-update'] = [$classe, 'updateBack', ['GET', 'POST'], 'admin'];
    $routes['admin-' . $entite . '-delete'] = [$classe, 'deleteBack', ['GET', 'POST'], 'admin'];
    $routes['admin-' . $entite . '-form'] = [$classe, isset($_GET['id']) ? 'updateBack' : 'addBack', ['GET'], 'admin'];
    $routes['admin-' . $entite . '-save'] = [$classe, isset($_POST['id']) && $_POST['id'] !== '' ? 'updateBack' : 'addBack', ['POST'], 'admin'];
}
foreach (['Bureau' => 'BureauPostalController', 'Colis' => 'ColisController', 'User' => 'CitoyenController'] as $entite => $classe) {
    $routes['add' . $entite] = [$classe, 'addBack', ['GET', 'POST'], 'admin'];
    $routes['update' . $entite] = [$classe, 'updateBack', ['GET', 'POST'], 'admin'];
    $routes['delete' . $entite] = [$classe, 'deleteBack', ['GET', 'POST'], 'admin'];
}
$routes['colis-accepter'] = ['ColisController', 'accepter', ['POST'], 'admin'];
foreach (['bureaux' => 'bureaux-poste', 'suivi' => 'suivi-colis', 'suiviColis' => 'suivi-colis',
          'envoi' => 'declarer-envoi', 'declarerEnvoi' => 'declarer-envoi', 'historique' => 'mes-colis',
          'mesColis' => 'mes-colis', 'connexion' => 'login', 'inscription' => 'signup', 'profil' => 'profile',
          'register' => 'signup', 'deconnexion' => 'logout'] as $alias => $cible) $routes[$alias] = $routes[$cible];
$routes['logout'][2] = ['POST'];
$routes['deconnexion'][2] = ['POST'];

require_once __DIR__ . '/Controllers/AuditController.php';
$routes['admin-audit'] = ['AuditController', 'listBack', ['GET'], 'admin'];

require_once __DIR__ . '/Controllers/GoogleController.php';
$routes['google-start'] = ['GoogleController', 'start', ['POST']];
$routes['google-callback'] = ['GoogleController', 'callback', ['GET']];

require_once __DIR__ . '/Controllers/AssureSocialController.php';
require_once __DIR__ . '/Controllers/BeneficiaireController.php';
require_once __DIR__ . '/Controllers/DemandeRemboursementController.php';
require_once __DIR__ . '/Controllers/HabilitationCNAMController.php';
require_once __DIR__ . '/Controllers/DocumentCNAMController.php';
$routes['mon-dossier'] = ['AssureSocialController', 'dossier', ['GET', 'POST'], ['citoyen']];
$routes['mes-beneficiaires'] = ['BeneficiaireController', 'liste', ['GET'], ['citoyen']];
$routes['beneficiaire-form'] = ['BeneficiaireController', 'formulaire', ['GET', 'POST'], ['citoyen']];
$routes['beneficiaire-delete'] = ['BeneficiaireController', 'supprimer', ['POST'], ['citoyen']];
$routes['mes-remboursements'] = ['DemandeRemboursementController', 'liste', ['GET'], ['citoyen']];
$routes['remboursement-form'] = ['DemandeRemboursementController', 'formulaire', ['GET', 'POST'], ['citoyen']];
$routes['remboursement-delete'] = ['DemandeRemboursementController', 'supprimer', ['POST'], ['citoyen']];
$routes['document-cnam'] = ['DocumentCNAMController', 'telecharger', ['GET'], ['citoyen', 'agent', 'admin']];
$routes['admin-assures'] = ['AssureSocialController', 'assures', ['GET'], ['agent', 'admin']];
$routes['admin-beneficiaires'] = ['BeneficiaireController', 'adminListe', ['GET'], ['agent', 'admin']];
$routes['admin-demandes-cnam'] = ['DemandeRemboursementController', 'adminListe', ['GET'], ['agent', 'admin']];
$routes['cnam-valider-beneficiaire'] = ['BeneficiaireController', 'traiterValidation', ['POST'], ['agent']];
$routes['cnam-prise-en-charge'] = ['DemandeRemboursementController', 'prendreEnCharge', ['POST'], ['agent']];
$routes['cnam-decision'] = ['DemandeRemboursementController', 'decision', ['POST'], ['agent']];
$routes['cnam-versement'] = ['DemandeRemboursementController', 'versement', ['POST'], ['agent']];
$routes['admin-cnam-agents'] = ['HabilitationCNAMController', 'liste', ['GET'], 'admin'];
$routes['cnam-habilitation'] = ['HabilitationCNAMController', 'modifier', ['POST'], 'admin'];

// Module Transport (Ranim Aissa) : lignes, stations et horaires.
require_once __DIR__ . '/Controllers/LigneTransportController.php';
require_once __DIR__ . '/Controllers/StationController.php';
require_once __DIR__ . '/Controllers/HoraireController.php';
$routes['lignes-transport'] = ['LigneTransportController', 'listFront', ['GET']];
$routes['stations-transport'] = ['StationController', 'listFront', ['GET']];
foreach (['ligne' => ['LigneTransportController', 'admin-lignes'],
          'station' => ['StationController', 'admin-stations'],
          'horaire' => ['HoraireController', 'admin-horaires']] as $entite => $module) {
    $routes[$module[1]] = [$module[0], 'listBack', ['GET'], 'admin'];
    $routes['admin-' . $entite . '-add'] = [$module[0], 'addBack', ['GET', 'POST'], 'admin'];
    $routes['admin-' . $entite . '-update'] = [$module[0], 'updateBack', ['GET', 'POST'], 'admin'];
    $routes['admin-' . $entite . '-delete'] = [$module[0], 'deleteBack', ['POST'], 'admin'];
}
