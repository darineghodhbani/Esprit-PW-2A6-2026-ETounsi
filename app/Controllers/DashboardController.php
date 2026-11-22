<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/ColisController.php';

class DashboardController extends Controller {

    public function index(): void {
        CitoyenController::requireAdmin();

        $this->renderBack('dashboard', [
            'title'       => 'Tableau de bord — E-Tounsi',
            'currentPage' => 'dashboard',
            'poste' => (new ColisController())->statistiques(),
        ]);
    }
}
