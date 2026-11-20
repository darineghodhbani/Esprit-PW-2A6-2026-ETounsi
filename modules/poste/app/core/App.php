<?php

require_once __DIR__ . '/fonctions.php';
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PosteController.php';
require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/BureauController.php';
require_once __DIR__ . '/../controllers/ColisController.php';
require_once __DIR__ . '/../controllers/UtilisateurController.php';
require_once __DIR__ . '/securite.php';

class App
{
    public function run()
    {
        $espace = $_GET['espace'] ?? 'front';
        $page = $_GET['page'] ?? '';
        if (!is_string($page)) {
            $page = 'introuvable';
        }
        if ($espace === 'admin') {
            exigerRole(['admin']);
            if ($page === '') {
                $page = 'dashboard';
            }
            $this->administration($page);
        } else {
            if ($espace !== 'front') {
                $page = 'introuvable';
            } elseif ($page === '') {
                $page = 'accueil';
            }
            $controller = new HomeController();
            $controller->index($page);
        }
    }

    private function verifierMethode($methode)
    {
        if ($_SERVER['REQUEST_METHOD'] === $methode) {
            return true;
        }
        header('Allow: ' . $methode);
        $controller = new Controller();
        $controller->problem(405, 'Méthode non autorisée', 'Utilisez les boutons du module pour effectuer cette action.');
        return false;
    }

    private function administration($page)
    {
        // Les noms des pages correspondent aux liens et aux actions des formulaires.
        switch ($page) {
            case 'dashboard':
                if ($this->verifierMethode('GET')) {
                    $controller = new AdminController();
                    $controller->index();
                }
                break;
            case 'bureaux':
                if ($this->verifierMethode('GET')) {
                    $controller = new BureauController();
                    $controller->index();
                }
                break;
            case 'bureau-form':
                if ($this->verifierMethode('GET')) {
                    $controller = new BureauController();
                    $controller->form();
                }
                break;
            case 'bureau-save':
                if ($this->verifierMethode('POST')) {
                    $controller = new BureauController();
                    $controller->save();
                }
                break;
            case 'bureau-suppression':
                if ($this->verifierMethode('GET')) {
                    $controller = new BureauController();
                    $controller->confirmDelete();
                }
                break;
            case 'bureau-delete':
                if ($this->verifierMethode('POST')) {
                    $controller = new BureauController();
                    $controller->delete();
                }
                break;
            case 'colis':
                if ($this->verifierMethode('GET')) {
                    $controller = new ColisController();
                    $controller->index();
                }
                break;
            case 'colis-form':
                if ($this->verifierMethode('GET')) {
                    $controller = new ColisController();
                    $controller->form();
                }
                break;
            case 'colis-accepter':
                if ($this->verifierMethode('POST')) {
                    $controller = new ColisController();
                    $controller->accepter();
                }
                break;
            case 'colis-save':
                if ($this->verifierMethode('POST')) {
                    $controller = new ColisController();
                    $controller->save();
                }
                break;
            case 'colis-suppression':
                if ($this->verifierMethode('GET')) {
                    $controller = new ColisController();
                    $controller->confirmDelete();
                }
                break;
            case 'colis-delete':
                if ($this->verifierMethode('POST')) {
                    $controller = new ColisController();
                    $controller->delete();
                }
                break;
            case 'utilisateurs':
                if ($this->verifierMethode('GET')) {
                    $controller = new UtilisateurController();
                    $controller->index();
                }
                break;
            case 'utilisateur-form':
                if ($this->verifierMethode('GET')) {
                    $controller = new UtilisateurController();
                    $controller->form();
                }
                break;
            case 'utilisateur-save':
                if ($this->verifierMethode('POST')) {
                    $controller = new UtilisateurController();
                    $controller->save();
                }
                break;
            case 'utilisateur-suppression':
                if ($this->verifierMethode('GET')) {
                    $controller = new UtilisateurController();
                    $controller->confirmDelete();
                }
                break;
            case 'utilisateur-delete':
                if ($this->verifierMethode('POST')) {
                    $controller = new UtilisateurController();
                    $controller->delete();
                }
                break;
            default:
                $controller = new Controller();
                $controller->problem(404, 'Page introuvable', 'Cette page du module Poste n’existe pas.');
                break;
        }
    }
}
