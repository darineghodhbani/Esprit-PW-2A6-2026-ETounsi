<?php

require_once __DIR__ . '/../../config.php';

class DemandeMunicipale {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

}
