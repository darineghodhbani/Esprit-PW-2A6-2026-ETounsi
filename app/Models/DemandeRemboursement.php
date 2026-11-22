<?php

class DemandeRemboursement {
    private ?int $id;
    private ?int $idBeneficiaire;
    private ?string $reference;
    private ?string $dateSoins;
    private ?string $natureSoins;
    private ?string $dateDepot;
    private ?string $montantDemande;
    private ?string $statut;
    private ?string $dateVersement;
    private ?string $referenceVersement;
    private ?int $version;
    private ?string $creationToken;

    public function __construct(?int $id, ?int $idBeneficiaire, ?string $reference, ?string $dateSoins, ?string $natureSoins, ?string $dateDepot, ?string $montantDemande, ?string $statut, ?string $dateVersement, ?string $referenceVersement, ?int $version, ?string $creationToken) {
        $this->id = $id;
        $this->idBeneficiaire = $idBeneficiaire;
        $this->reference = $reference;
        $this->dateSoins = $dateSoins;
        $this->natureSoins = $natureSoins;
        $this->dateDepot = $dateDepot;
        $this->montantDemande = $montantDemande;
        $this->statut = $statut;
        $this->dateVersement = $dateVersement;
        $this->referenceVersement = $referenceVersement;
        $this->version = $version;
        $this->creationToken = $creationToken;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdBeneficiaire(): ?int {
        return $this->idBeneficiaire;
    }

    public function setIdBeneficiaire(?int $idBeneficiaire): void {
        $this->idBeneficiaire = $idBeneficiaire;
    }

    public function getReference(): ?string {
        return $this->reference;
    }

    public function setReference(?string $reference): void {
        $this->reference = $reference;
    }

    public function getDateSoins(): ?string {
        return $this->dateSoins;
    }

    public function setDateSoins(?string $dateSoins): void {
        $this->dateSoins = $dateSoins;
    }

    public function getNatureSoins(): ?string {
        return $this->natureSoins;
    }

    public function setNatureSoins(?string $natureSoins): void {
        $this->natureSoins = $natureSoins;
    }

    public function getDateDepot(): ?string {
        return $this->dateDepot;
    }

    public function setDateDepot(?string $dateDepot): void {
        $this->dateDepot = $dateDepot;
    }

    public function getMontantDemande(): ?string {
        return $this->montantDemande;
    }

    public function setMontantDemande(?string $montantDemande): void {
        $this->montantDemande = $montantDemande;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }

    public function getDateVersement(): ?string {
        return $this->dateVersement;
    }

    public function setDateVersement(?string $dateVersement): void {
        $this->dateVersement = $dateVersement;
    }

    public function getReferenceVersement(): ?string {
        return $this->referenceVersement;
    }

    public function setReferenceVersement(?string $referenceVersement): void {
        $this->referenceVersement = $referenceVersement;
    }

    public function getVersion(): ?int {
        return $this->version;
    }

    public function setVersion(?int $version): void {
        $this->version = $version;
    }

    public function getCreationToken(): ?string {
        return $this->creationToken;
    }

    public function setCreationToken(?string $creationToken): void {
        $this->creationToken = $creationToken;
    }
}
