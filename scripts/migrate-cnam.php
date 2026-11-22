<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../app/Controllers/InstallationCNAMController.php';
try { (new InstallationCNAMController())->migrer(); echo "Migration CNAM/Google appliquée ou déjà à jour.\n"; }
catch (Throwable $e) { fwrite(STDERR,$e->getMessage() . "\n"); exit(1); }
