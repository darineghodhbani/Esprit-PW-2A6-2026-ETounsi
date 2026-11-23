<?php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = ['host' => '127.0.0.1', 'port' => 3306, 'database' => 'etounsi_validation', 'user' => 'root', 'password' => ''];
            if (is_file(__DIR__ . '/config.local.php')) {
                $config = array_merge($config, require __DIR__ . '/config.local.php');
            }
            self::$instance = new PDO(
                'mysql:host=' . $config['host'] . ';port=' . (int) ($config['port'] ?? 3306)
                    . ';dbname=' . $config['database'] . ';charset=utf8mb4',
                $config['user'], $config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                 PDO::ATTR_EMULATE_PREPARES => false]
            );
        }
        return self::$instance;
    }
}
