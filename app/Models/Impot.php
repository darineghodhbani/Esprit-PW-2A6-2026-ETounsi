<?php

class Impot {
    private ?int $id;
    private ?int $idPropriete;
    private ?int $annee;
    private ?int $montant;
    private ?string $dateLimite;
    private ?string $statut;

    public function __construct(?int $id = null, ?int $idPropriete = null, ?int $annee = null, ?int $montant = null, ?string $dateLimite = null, ?string $statut = null) {
        $this->id = $id;
        $this->idPropriete = $idPropriete;
        $this->annee = $annee;
        $this->montant = $montant;
        $this->dateLimite = $dateLimite;
        $this->statut = $statut;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdPropriete(): ?int {
        return $this->idPropriete;
    }

    public function setIdPropriete(?int $idPropriete): void {
        $this->idPropriete = $idPropriete;
    }

    public function getAnnee(): ?int {
        return $this->annee;
    }

    public function setAnnee(?int $annee): void {
        $this->annee = $annee;
    }

    public function getMontant(): ?int {
        return $this->montant;
    }

    public function setMontant(?int $montant): void {
        $this->montant = $montant;
    }

    public function getDateLimite(): ?string {
        return $this->dateLimite;
    }

    public function setDateLimite(?string $dateLimite): void {
        $this->dateLimite = $dateLimite;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }


    public function listerImpots() {
        $sql = "SELECT impot.*, propriete.reference FROM impot
                JOIN propriete ON impot.id_propriete = propriete.id
                ORDER BY impot.id ASC";
        $db = Database::getConnection();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function listerImpotsCitoyen($idCitoyen) {
        $sql = "SELECT impot.*, propriete.reference FROM impot
                JOIN propriete ON impot.id_propriete = propriete.id
                WHERE propriete.id_citoyen = :id_citoyen
                ORDER BY impot.id ASC";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_citoyen' => $idCitoyen]);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function listerImpotsPayes($idCitoyen) {
        $sql = "SELECT impot.*, propriete.reference, propriete.adresse FROM impot
                JOIN propriete ON impot.id_propriete = propriete.id
                WHERE propriete.id_citoyen = :id_citoyen AND impot.statut = 'paye'
                ORDER BY impot.id ASC";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_citoyen' => $idCitoyen]);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function recupererImpot($id) {
        $sql = "SELECT * FROM impot WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function compterImpots($statut) {
        $sql = "SELECT COUNT(*) FROM impot WHERE statut = :statut";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['statut' => $statut]);
            return $query->fetchColumn();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function ajouterImpot(Impot $impot) {
        $sql = "INSERT INTO impot (id_propriete, annee, montant, date_limite, statut)
                VALUES (:id_propriete, :annee, :montant, :date_limite, :statut)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_propriete' => $impot->getIdPropriete(),
                'annee' => $impot->getAnnee(),
                'montant' => $impot->getMontant(),
                'date_limite' => $impot->getDateLimite(),
                'statut' => $impot->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function modifierImpot(Impot $impot, $id) {
        $sql = "UPDATE impot SET id_propriete = :id_propriete, annee = :annee, montant = :montant,
                date_limite = :date_limite, statut = :statut WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_propriete' => $impot->getIdPropriete(),
                'annee' => $impot->getAnnee(),
                'montant' => $impot->getMontant(),
                'date_limite' => $impot->getDateLimite(),
                'statut' => $impot->getStatut(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function supprimerImpot($id) {
        $sql = "DELETE FROM impot WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function recupererImpotCitoyen($id, $idCitoyen) {
        $sql = "SELECT impot.* FROM impot
                JOIN propriete ON impot.id_propriete = propriete.id
                WHERE impot.id = :id AND propriete.id_citoyen = :id_citoyen";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id, 'id_citoyen' => $idCitoyen]);
            return $query->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function changerStatut($id, $statut) {
        $sql = "UPDATE impot SET statut = :statut WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['statut' => $statut, 'id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
