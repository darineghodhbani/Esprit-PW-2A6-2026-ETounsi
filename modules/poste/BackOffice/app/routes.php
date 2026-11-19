<?php

function verifierMethode($methode)
{
    if ($_SERVER['REQUEST_METHOD'] === $methode) {
        return true;
    }

    header('Allow: ' . $methode);
    $controller = new Controller();
    $controller->problem(405, 'Méthode non autorisée', 'Utilisez les boutons du module pour effectuer cette action.');
    return false;
}

$page = 'dashboard';
if (isset($_GET['page'])) {
    $page = $_GET['page'];
}

// Le contrôle du rôle s'applique aussi lorsqu'une URL est saisie directement.
$pagesUtilisateurs = ['utilisateurs', 'utilisateur-form', 'utilisateur-save', 'utilisateur-suppression', 'utilisateur-delete'];
if (in_array($page, $pagesUtilisateurs, true)) {
    exigerRole(['super_admin']);
}

// Les noms des pages correspondent aux liens et aux actions des formulaires.
switch ($page) {
    case 'dashboard':
        if (verifierMethode('GET')) {
            $controller = new DashboardController();
            $controller->index();
        }
        break;
    case 'bureaux':
        if (verifierMethode('GET')) {
            $controller = new BureauController();
            $controller->index();
        }
        break;
    case 'bureau-form':
        if (verifierMethode('GET')) {
            $controller = new BureauController();
            $controller->form();
        }
        break;
    case 'bureau-save':
        if (verifierMethode('POST')) {
            $controller = new BureauController();
            $controller->save();
        }
        break;
    case 'bureau-suppression':
        if (verifierMethode('GET')) {
            $controller = new BureauController();
            $controller->confirmDelete();
        }
        break;
    case 'bureau-delete':
        if (verifierMethode('POST')) {
            $controller = new BureauController();
            $controller->delete();
        }
        break;
    case 'colis':
        if (verifierMethode('GET')) {
            $controller = new ColisController();
            $controller->index();
        }
        break;
    case 'colis-form':
        if (verifierMethode('GET')) {
            $controller = new ColisController();
            $controller->form();
        }
        break;
    case 'colis-save':
        if (verifierMethode('POST')) {
            $controller = new ColisController();
            $controller->save();
        }
        break;
    case 'colis-suppression':
        if (verifierMethode('GET')) {
            $controller = new ColisController();
            $controller->confirmDelete();
        }
        break;
    case 'colis-delete':
        if (verifierMethode('POST')) {
            $controller = new ColisController();
            $controller->delete();
        }
        break;
    case 'utilisateurs':
        if (verifierMethode('GET')) {
            $controller = new UtilisateursController();
            $controller->index();
        }
        break;
    case 'utilisateur-form':
        if (verifierMethode('GET')) {
            $controller = new UtilisateursController();
            $controller->form();
        }
        break;
    case 'utilisateur-save':
        if (verifierMethode('POST')) {
            $controller = new UtilisateursController();
            $controller->save();
        }
        break;
    case 'utilisateur-suppression':
        if (verifierMethode('GET')) {
            $controller = new UtilisateursController();
            $controller->confirmDelete();
        }
        break;
    case 'utilisateur-delete':
        if (verifierMethode('POST')) {
            $controller = new UtilisateursController();
            $controller->delete();
        }
        break;
    default:
        $controller = new Controller();
        $controller->problem(404, 'Page introuvable', 'Cette page du module Poste n’existe pas.');
        break;
}
