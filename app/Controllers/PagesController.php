<?php

require_once __DIR__ . '/Controller.php';

class PagesController extends Controller {

    public function administrations(): void {
        $modules = require __DIR__ . '/../modules.php';

        $title = 'Les administrations — E-Tounsi';
        $currentPage = 'administrations';
        require __DIR__ . '/../Views/frontoffice/administrations.php';
    }

    public function module(): void {
        $modules = require __DIR__ . '/../modules.php';
        $id = $_GET['id'] ?? '';
        if (!isset($modules[$id])) {
            header('Location: index.php?page=administrations');
            exit;
        }
        $module = $modules[$id];

        $title = m($module, 'label') . ' — E-Tounsi';
        $currentPage = 'administrations';
        require __DIR__ . '/../Views/frontoffice/module.php';
    }

    public function switchLang(): void {
        if (in_array($_GET['to'] ?? '', ET_LANGS, true)) {
            $_SESSION['lang'] = $_GET['to'];
        }
        header('Location: index.php?page=home');
        exit;
    }

    public function aide(): void {
        $this->info('aide', 'Besoin d\'aide ? — E-Tounsi');
    }

    public function projet(): void {
        $this->info('projet', 'À propos du projet — E-Tounsi');
    }

    public function confidentialite(): void {
        $this->info('confidentialite', 'Confidentialité — E-Tounsi');
    }

    private function info(string $info, string $title): void {
        $currentPage = $info;
        require __DIR__ . '/../Views/frontoffice/info.php';
    }
}
