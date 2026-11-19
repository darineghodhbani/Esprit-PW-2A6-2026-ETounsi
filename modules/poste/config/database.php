<?php

class Config
{
    private static $connexion = null;

    public static function parametres()
    {
        $parametres = [
            'hote' => '127.0.0.1',
            'port' => 3306,
            'base' => 'etounsi_poste',
            'utilisateur' => 'root',
            'mot_de_passe' => '',
        ];

        if (file_exists(__DIR__ . '/local.php')) {
            $local = require __DIR__ . '/local.php';
            $parametres = array_merge($parametres, $local);
        }

        return $parametres;
    }

    public static function getConnexion()
    {
        if (self::$connexion === null) {
            $parametres = self::parametres();
            $dsn = 'mysql:host=' . $parametres['hote'] . ';port=' . $parametres['port']
                . ';dbname=' . $parametres['base'] . ';charset=utf8mb4';

            try {
                self::$connexion = new PDO($dsn, $parametres['utilisateur'], $parametres['mot_de_passe'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $erreur) {
                error_log($erreur->getMessage());
                http_response_code(503);
                exit('La base MySQL est indisponible. Démarrez MySQL dans XAMPP et vérifiez la configuration.');
            }
        }

        return self::$connexion;
    }
}
