<?php

require_once __DIR__ . '/../../config.php';

class Citoyen {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(string $nom, string $prenom, string $email,
                           string $telephone, string $motDePasse) {
        if ($this->findByEmail($email)) {
            return false;
        }
        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'INSERT INTO citoyen (nom, prenom, email, telephone, mot_de_passe)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nom, $prenom, $email, $telephone, $hash]);
        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email) {
        $stmt = $this->db->prepare('SELECT * FROM citoyen WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM citoyen WHERE id_citoyen = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function verifyLogin(string $email, string $motDePasse) {
        $citoyen = $this->findByEmail($email);
        if ($citoyen && password_verify($motDePasse, $citoyen['mot_de_passe'])) {
            return $citoyen;
        }
        return false;
    }
}
