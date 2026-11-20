<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Models/Propriete.php';

class ProprieteController
{
    private PDO $db;

    public const TYPES = [
        'maison' => 'Maison', 'appartement' => 'Appartement', 'terrain' => 'Terrain',
        'voiture' => 'Voiture', 'local_commercial' => 'Local commercial',
    ];

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS proprietaire
             FROM propriete p JOIN users u ON u.id = p.id_user
             ORDER BY p.id ASC"
        );
        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => Propriete::fromArray($row), $rows);
    }

    public function findByUser(int $idUser): array
    {
        $stmt = $this->db->prepare('SELECT * FROM propriete WHERE id_user = :id_user ORDER BY id ASC');
        $stmt->execute(['id_user' => $idUser]);
        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => Propriete::fromArray($row), $rows);
    }

    public function findById(int $id): ?Propriete
    {
        $stmt = $this->db->prepare('SELECT * FROM propriete WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Propriete::fromArray($row) : null;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM propriete')->fetchColumn();
    }

    public function create(Propriete $p): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO propriete (id_user, type, reference, adresse, valeur)
             VALUES (:id_user, :type, :reference, :adresse, :valeur)'
        );
        $stmt->execute([
            'id_user'   => $p->getIdUser(),
            'type'      => $p->getType(),
            'reference' => $p->getReference(),
            'adresse'   => $p->getAdresse(),
            'valeur'    => $p->getValeur(),
        ]);
    }

    public function update(Propriete $p): void
    {
        $stmt = $this->db->prepare(
            'UPDATE propriete SET id_user = :id_user, type = :type, reference = :reference,
                    adresse = :adresse, valeur = :valeur
             WHERE id = :id'
        );
        $stmt->execute([
            'id_user'   => $p->getIdUser(),
            'type'      => $p->getType(),
            'reference' => $p->getReference(),
            'adresse'   => $p->getAdresse(),
            'valeur'    => $p->getValeur(),
            'id'        => $p->getId(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM propriete WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function clean(array $post): array
    {
        return [
            'id_user'   => (int) ($post['id_user'] ?? 0),
            'type'      => trim((string) ($post['type'] ?? '')),
            'reference' => trim((string) ($post['reference'] ?? '')),
            'adresse'   => trim((string) ($post['adresse'] ?? '')),
            'valeur'    => trim((string) ($post['valeur'] ?? '')),
        ];
    }

    public function validate(array $d): array
    {
        $errors = [];

        if ($d['id_user'] <= 0) {
            $errors['id_user'] = 'Veuillez choisir un citoyen.';
        }

        if (!isset(self::TYPES[$d['type']])) {
            $errors['type'] = 'Veuillez choisir un type.';
        }

        if ($d['reference'] === '') {
            $errors['reference'] = 'La référence est obligatoire.';
        } elseif (strlen($d['reference']) < 3 || strlen($d['reference']) > 50) {
            $errors['reference'] = 'La référence doit contenir 3 à 50 caractères.';
        }

        if ($d['adresse'] === '') {
            $errors['adresse'] = "L'adresse est obligatoire.";
        } elseif (strlen($d['adresse']) < 5 || strlen($d['adresse']) > 150) {
            $errors['adresse'] = "L'adresse doit contenir 5 à 150 caractères.";
        }

        if ($d['valeur'] === '') {
            $errors['valeur'] = 'La valeur est obligatoire.';
        } elseif (!preg_match('/^[0-9]+$/', $d['valeur']) || (int) $d['valeur'] <= 0) {
            $errors['valeur'] = 'La valeur doit être un nombre entier positif.';
        }

        return $errors;
    }
}
