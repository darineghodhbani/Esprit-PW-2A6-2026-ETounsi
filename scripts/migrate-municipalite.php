<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';

try {
    $sql = file_get_contents(__DIR__ . '/../database/municipalite-integration.sql');
    if ($sql === false) {
        throw new RuntimeException('Fichier de migration Municipalité introuvable.');
    }
    Database::getConnection()->exec($sql);
    echo "Module Municipalité installé ou mis à jour. Texte UTF-8 vérifié par la connexion PDO.\n";
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(1);
}
