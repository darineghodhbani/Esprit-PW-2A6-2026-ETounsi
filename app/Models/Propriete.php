<?php

class Propriete {
    private ?int $id;
    private ?int $idCitoyen;
    private ?string $type;
    private ?string $reference;
    private ?string $adresse;
    private ?int $valeur;

    public function __construct(?int $id = null, ?int $idCitoyen = null, ?string $type = null, ?string $reference = null, ?string $adresse = null, ?int $valeur = null) {
        $this->id = $id;
        $this->idCitoyen = $idCitoyen;
        $this->type = $type;
        $this->reference = $reference;
        $this->adresse = $adresse;
        $this->valeur = $valeur;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdCitoyen(): ?int {
        return $this->idCitoyen;
    }

    public function setIdCitoyen(?int $idCitoyen): void {
        $this->idCitoyen = $idCitoyen;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): void {
        $this->type = $type;
    }

    public function getReference(): ?string {
        return $this->reference;
    }

    public function setReference(?string $reference): void {
        $this->reference = $reference;
    }

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void {
        $this->adresse = $adresse;
    }

    public function getValeur(): ?int {
        return $this->valeur;
    }

    public function setValeur(?int $valeur): void {
        $this->valeur = $valeur;
    }


    public function listerProprietes() {
        $sql = "SELECT propriete.*, CONCAT(citoyen.prenom, ' ', citoyen.nom) AS proprietaire
                FROM propriete JOIN citoyen ON propriete.id_citoyen = citoyen.id_citoyen
                ORDER BY propriete.id ASC";
        $db = Database::getConnection();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function listerProprietesCitoyen($idCitoyen) {
        $sql = "SELECT * FROM propriete WHERE id_citoyen = :id_citoyen ORDER BY id ASC";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_citoyen' => $idCitoyen]);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function recupererPropriete($id) {
        $sql = "SELECT * FROM propriete WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function compterProprietes() {
        $sql = "SELECT COUNT(*) FROM propriete";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetchColumn();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function listerCitoyens() {
        $sql = "SELECT id_citoyen, nom, prenom FROM citoyen WHERE role = 'citoyen' ORDER BY nom ASC";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function ajouterPropriete(Propriete $propriete) {
        $sql = "INSERT INTO propriete (id_citoyen, type, reference, adresse, valeur)
                VALUES (:id_citoyen, :type, :reference, :adresse, :valeur)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_citoyen' => $propriete->getIdCitoyen(),
                'type' => $propriete->getType(),
                'reference' => $propriete->getReference(),
                'adresse' => $propriete->getAdresse(),
                'valeur' => $propriete->getValeur()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function modifierPropriete(Propriete $propriete, $id) {
        $sql = "UPDATE propriete SET id_citoyen = :id_citoyen, type = :type, reference = :reference,
                adresse = :adresse, valeur = :valeur WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_citoyen' => $propriete->getIdCitoyen(),
                'type' => $propriete->getType(),
                'reference' => $propriete->getReference(),
                'adresse' => $propriete->getAdresse(),
                'valeur' => $propriete->getValeur(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function supprimerPropriete($id) {
        $sql = "DELETE FROM propriete WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
