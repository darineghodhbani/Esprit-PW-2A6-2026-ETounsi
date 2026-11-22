<?php

require_once __DIR__ . '/../../config.php';

class Colis {
    public const STATUTS = ['En attente', 'Accepté', 'En transit', 'Livré', 'Annulé'];

    private ?int $id;
    private ?int $idBureau;
    private ?int $idCitoyen;
    private ?string $numeroSuivi;
    private ?string $statut;
    private ?string $nomCitoyen;
    private ?string $dateDeclaration;

    public function __construct(?int $id = null, ?int $idBureau = null, ?int $idCitoyen = null, ?string $numeroSuivi = null, ?string $statut = null, ?string $nomCitoyen = null, ?string $dateDeclaration = null) {
        $this->id = $id;
        $this->idBureau = $idBureau;
        $this->idCitoyen = $idCitoyen;
        $this->numeroSuivi = $numeroSuivi;
        $this->statut = $statut;
        $this->nomCitoyen = $nomCitoyen;
        $this->dateDeclaration = $dateDeclaration;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdBureau(): ?int {
        return $this->idBureau;
    }

    public function setIdBureau(?int $idBureau): void {
        $this->idBureau = $idBureau;
    }

    public function getIdCitoyen(): ?int {
        return $this->idCitoyen;
    }

    public function setIdCitoyen(?int $idCitoyen): void {
        $this->idCitoyen = $idCitoyen;
    }

    public function getNumeroSuivi(): ?string {
        return $this->numeroSuivi;
    }

    public function setNumeroSuivi(?string $numeroSuivi): void {
        $this->numeroSuivi = $numeroSuivi;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }

    public function getNomCitoyen(): ?string {
        return $this->nomCitoyen;
    }

    public function setNomCitoyen(?string $nomCitoyen): void {
        $this->nomCitoyen = $nomCitoyen;
    }

    public function getDateDeclaration(): ?string {
        return $this->dateDeclaration;
    }

    public function setDateDeclaration(?string $dateDeclaration): void {
        $this->dateDeclaration = $dateDeclaration;
    }

    public function listerColis() {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau
                ORDER BY c.created_at DESC, c.id_colis DESC";
        $db = Database::getConnection();
        return $db->query($sql)->fetchAll();
    }

    public function compterColis($statut = null) {
        $db = Database::getConnection();
        if ($statut === null) {
            $sql = "SELECT COUNT(*) FROM colis";
            return (int) $db->query($sql)->fetchColumn();
        }
        $sql = "SELECT COUNT(*) FROM colis WHERE statut = :statut";
        $query = $db->prepare($sql);
        $query->execute(['statut' => $statut]);
        return (int) $query->fetchColumn();
    }

    public function listerColisCitoyen($idCitoyen) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau
                WHERE c.id_citoyen = :id_citoyen ORDER BY c.created_at DESC, c.id_colis DESC";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id_citoyen' => $idCitoyen]);
        return $query->fetchAll();
    }

    public function recupererColis($id) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau WHERE c.id_colis = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        $colis = $query->fetch();
        return $colis === false ? null : $colis;
    }

    public function recupererColisCitoyen($id, $idCitoyen) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau
                WHERE c.id_colis = :id AND c.id_citoyen = :id_citoyen";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id, 'id_citoyen' => $idCitoyen]);
        $colis = $query->fetch();
        return $colis === false ? null : $colis;
    }

    public function chercherColis($numero) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau WHERE c.numero_suivi = :numero";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['numero' => strtoupper(trim($numero))]);
        $colis = $query->fetch();
        return $colis === false ? null : $colis;
    }

    public function listerCitoyens() {
        $sql = "SELECT id_citoyen, nom, prenom FROM citoyen WHERE role = 'citoyen' AND actif = 1 ORDER BY nom";
        $db = Database::getConnection();
        return $db->query($sql)->fetchAll();
    }

    public function ajouterColis(Colis $colis) {
        $sql = "INSERT INTO colis (id_bureau_depart, id_citoyen, numero_suivi, statut, nom_citoyen, date_declaration)
                VALUES (:id_bureau, :id_citoyen, :numero, :statut, :nom, :date_declaration)";
        $db = Database::getConnection();
        if ($colis->getNomCitoyen() === null) {
            $query = $db->prepare('SELECT nom, prenom FROM citoyen WHERE id_citoyen = :id');
            $query->execute(['id' => $colis->getIdCitoyen()]);
            $citoyen = $query->fetch();
            $colis->setNomCitoyen(trim(($citoyen['prenom'] ?? '') . ' ' . ($citoyen['nom'] ?? '')));
        }
        if ($colis->getDateDeclaration() === null) $colis->setDateDeclaration(date('Y-m-d'));
        $colis->setStatut('En attente');
        $db->beginTransaction();
        try {
            $query = $db->prepare($sql);
            do {
                $colis->setNumeroSuivi('ET' . strtoupper(bin2hex(random_bytes(5))) . 'TN');
                try {
                    $query->execute([
                        'id_bureau' => $colis->getIdBureau(), 'id_citoyen' => $colis->getIdCitoyen(),
                        'numero' => $colis->getNumeroSuivi(), 'statut' => $colis->getStatut(),
                        'nom' => $colis->getNomCitoyen(), 'date_declaration' => $colis->getDateDeclaration(),
                    ]);
                    $enregistre = true;
                } catch (PDOException $e) {
                    if (!isset($e->errorInfo[1]) || (int) $e->errorInfo[1] !== 1062) throw $e;
                    $enregistre = false;
                }
            } while (!$enregistre);
            $colis->setId((int) $db->lastInsertId());
            $resultat = $this->recupererColis($colis->getId());
            $db->commit();
            return $resultat;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function modifierColis(Colis $colis, $id, $statutActuel) {
        if (!in_array($colis->getStatut(), self::statutsSuivants($statutActuel), true)) return false;
        $sql = "UPDATE colis SET statut = :statut WHERE id_colis = :id AND statut = :statut_actuel";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['statut' => $colis->getStatut(), 'id' => $id, 'statut_actuel' => $statutActuel]);
        return $query->rowCount() === 1;
    }

    public function modifierColisCitoyen(Colis $colis, $id) {
        $sql = "UPDATE colis SET id_bureau_depart = :id_bureau, nom_citoyen = :nom, date_declaration = :date_declaration
                WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute([
            'id_bureau' => $colis->getIdBureau(), 'nom' => $colis->getNomCitoyen(),
            'date_declaration' => $colis->getDateDeclaration(), 'id' => $id,
            'id_citoyen' => $colis->getIdCitoyen(), 'statut' => 'En attente',
        ]);
        return $query->rowCount() === 1;
    }

    public function annulerColis($id, $idCitoyen) {
        $sql = "UPDATE colis SET statut = :annule WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :attente";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['annule' => 'Annulé', 'id' => $id, 'id_citoyen' => $idCitoyen, 'attente' => 'En attente']);
        return $query->rowCount() === 1;
    }

    public function supprimerColis($id) {
        $sql = "DELETE FROM colis WHERE id_colis = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->rowCount() === 1;
    }

    public function supprimerColisCitoyen($id, $idCitoyen) {
        $sql = "DELETE FROM colis WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id, 'id_citoyen' => $idCitoyen, 'statut' => 'En attente']);
        return $query->rowCount() === 1;
    }

    public static function statutsSuivants($statut) {
        $transitions = [
            'En attente' => ['Accepté', 'Annulé'],
            'Accepté' => ['En transit', 'Annulé'],
            'En transit' => ['Livré'],
        ];
        return $transitions[$statut] ?? [];
    }
}
