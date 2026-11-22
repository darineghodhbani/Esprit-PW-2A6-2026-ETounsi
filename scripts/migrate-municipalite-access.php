<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config.php';
try {
    $sql = file_get_contents(__DIR__ . '/../database/municipalite-acces-role.sql');
    if ($sql === false) throw new RuntimeException('Fichier de migration introuvable.');
    Database::getConnection()->exec($sql);
    echo "Droits d’accès Municipalité installés ou déjà à jour.\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
