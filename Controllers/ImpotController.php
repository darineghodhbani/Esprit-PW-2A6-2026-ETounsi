<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Models/Impot.php';

class ImpotController
{
    private PDO $db;

    public const STATUTS = ['non_paye' => 'Non payé', 'en_attente' => 'En attente de validation', 'paye' => 'Payé'];

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT i.*, p.reference
             FROM impot i JOIN propriete p ON p.id = i.id_propriete
             ORDER BY i.id ASC'
        );
        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => Impot::fromArray($row), $rows);
    }

    public function findByUser(int $idUser): array
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, p.reference
             FROM impot i JOIN propriete p ON p.id = i.id_propriete
             WHERE p.id_user = :id_user
             ORDER BY i.id ASC'
        );
        $stmt->execute(['id_user' => $idUser]);
        $rows = $stmt->fetchAll();
        return array_map(fn(array $row) => Impot::fromArray($row), $rows);
    }

    public function findById(int $id): ?Impot
    {
        $stmt = $this->db->prepare('SELECT * FROM impot WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Impot::fromArray($row) : null;
    }

    public function countByStatut(string $statut): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM impot WHERE statut = :statut');
        $stmt->execute(['statut' => $statut]);
        return (int) $stmt->fetchColumn();
    }

    public function create(Impot $i): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO impot (id_propriete, annee, montant, date_limite, statut)
             VALUES (:id_propriete, :annee, :montant, :date_limite, :statut)'
        );
        $stmt->execute([
            'id_propriete' => $i->getIdPropriete(),
            'annee'        => $i->getAnnee(),
            'montant'      => $i->getMontant(),
            'date_limite'  => $i->getDateLimite(),
            'statut'       => $i->getStatut(),
        ]);
    }

    public function update(Impot $i): void
    {
        $stmt = $this->db->prepare(
            'UPDATE impot SET id_propriete = :id_propriete, annee = :annee, montant = :montant,
                    date_limite = :date_limite, statut = :statut
             WHERE id = :id'
        );
        $stmt->execute([
            'id_propriete' => $i->getIdPropriete(),
            'annee'        => $i->getAnnee(),
            'montant'      => $i->getMontant(),
            'date_limite'  => $i->getDateLimite(),
            'statut'       => $i->getStatut(),
            'id'           => $i->getId(),
        ]);
    }

    public function demanderPaiement(int $id, int $idUser): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE impot SET statut = 'en_attente'
             WHERE id = :id AND statut = 'non_paye'
               AND id_propriete IN (SELECT id FROM propriete WHERE id_user = :id_user)"
        );
        $stmt->execute(['id' => $id, 'id_user' => $idUser]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM impot WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function clean(array $post): array
    {
        return [
            'id_propriete' => (int) ($post['id_propriete'] ?? 0),
            'annee'        => trim((string) ($post['annee'] ?? '')),
            'montant'      => trim((string) ($post['montant'] ?? '')),
            'date_limite'  => trim((string) ($post['date_limite'] ?? '')),
            'statut'       => trim((string) ($post['statut'] ?? '')),
        ];
    }

    public function validate(array $d): array
    {
        $errors = [];

        if ($d['id_propriete'] <= 0) {
            $errors['id_propriete'] = 'Veuillez choisir une propriété.';
        }

        if ($d['annee'] === '') {
            $errors['annee'] = "L'année est obligatoire.";
        } elseif (!preg_match('/^[0-9]{4}$/', $d['annee']) || (int) $d['annee'] < 2000 || (int) $d['annee'] > 2100) {
            $errors['annee'] = "L'année doit être comprise entre 2000 et 2100.";
        }

        if ($d['montant'] === '') {
            $errors['montant'] = 'Le montant est obligatoire.';
        } elseif (!preg_match('/^[0-9]+$/', $d['montant']) || (int) $d['montant'] <= 0) {
            $errors['montant'] = 'Le montant doit être un nombre entier positif.';
        }

        if ($d['date_limite'] === '') {
            $errors['date_limite'] = 'La date limite est obligatoire.';
        }

        if (!isset(self::STATUTS[$d['statut']])) {
            $errors['statut'] = 'Veuillez choisir un statut.';
        }

        return $errors;
    }
}
