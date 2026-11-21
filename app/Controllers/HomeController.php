<?php

require_once __DIR__ . '/Controller.php';

class HomeController extends Controller {

    public function index(): void {
        $modules = require __DIR__ . '/../modules.php';

        $title = 'Accueil — E-Tounsi';
        $currentPage = 'home';
        require __DIR__ . '/../Views/frontoffice/home.php';
    }
}
