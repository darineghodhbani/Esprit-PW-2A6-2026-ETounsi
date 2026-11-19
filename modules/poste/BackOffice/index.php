<?php

require_once dirname(__DIR__) . '/config.php';

exigerRole(['admin_poste', 'super_admin']);

require_once __DIR__ . '/app/Controllers/Controller.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Controllers/BureauController.php';
require_once __DIR__ . '/app/Controllers/ColisController.php';
require_once __DIR__ . '/app/Controllers/UtilisateursController.php';

header('Cache-Control: no-store');
require_once __DIR__ . '/app/routes.php';
