<?php

require_once __DIR__ . '/../../config.php';

class BureauPostal {
    private ?int $id;
    private ?string $nom;
    private ?string $adresse;
    private ?string $ville;

    public function __construct(?int $id = null, ?string $nom = null, ?string $adresse = null, ?string $ville = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->ville = $ville;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void {
        $this->adresse = $adresse;
    }

    public function getVille(): ?string {
        return $this->ville;
    }

    public function setVille(?string $ville): void {
        $this->ville = $ville;
    }

    public function listerBureaux() {
        $sql = "SELECT * FROM bureau_postal ORDER BY ville, nom";
        $db = Database::getConnection();
        return $db->query($sql)->fetchAll();
    }

    public function recupererBureau($id) {
        $sql = "SELECT * FROM bureau_postal WHERE id_bureau = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        $bureau = $query->fetch();
        return $bureau === false ? null : $bureau;
    }

    public function compterBureaux() {
        $sql = "SELECT COUNT(*) FROM bureau_postal";
        $db = Database::getConnection();
        return (int) $db->query($sql)->fetchColumn();
    }

    public function ajouterBureau(BureauPostal $bureau) {
        $sql = "INSERT INTO bureau_postal (nom, adresse, ville) VALUES (:nom, :adresse, :ville)";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute([
            'nom' => $bureau->getNom(),
            'adresse' => $bureau->getAdresse(),
            'ville' => $bureau->getVille(),
        ]);
        return (int) $db->lastInsertId();
    }

    public function modifierBureau(BureauPostal $bureau, $id) {
        $sql = "UPDATE bureau_postal SET nom = :nom, adresse = :adresse, ville = :ville WHERE id_bureau = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute([
            'nom' => $bureau->getNom(),
            'adresse' => $bureau->getAdresse(),
            'ville' => $bureau->getVille(),
            'id' => $id,
        ]);
        return $query->rowCount() === 1 || $this->recupererBureau($id) !== null;
    }

    public function supprimerBureau($id) {
        $sql = "DELETE FROM bureau_postal WHERE id_bureau = :id
                AND id_bureau NOT IN (SELECT id_bureau_depart FROM colis)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount() === 1;
        } catch (PDOException $e) {
            if (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1451) return false;
            throw $e;
        }
    }
}
