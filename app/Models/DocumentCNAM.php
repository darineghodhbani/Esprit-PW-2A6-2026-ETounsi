<?php

class DocumentCNAM {
    private ?int $idDocument;
    private ?string $nomFichier;
    private ?string $chemin;
    private ?string $typeDocument;
    private ?string $dateUpload;
    private ?int $idRemboursement;
    private ?int $idBeneficiaire;

    public function __construct(?int $idDocument, ?string $nomFichier, ?string $chemin, ?string $typeDocument, ?string $dateUpload, ?int $idRemboursement, ?int $idBeneficiaire) {
        $this->idDocument = $idDocument;
        $this->nomFichier = $nomFichier;
        $this->chemin = $chemin;
        $this->typeDocument = $typeDocument;
        $this->dateUpload = $dateUpload;
        $this->idRemboursement = $idRemboursement;
        $this->idBeneficiaire = $idBeneficiaire;
    }

    public function getIdDocument(): ?int {
        return $this->idDocument;
    }

    public function setIdDocument(?int $idDocument): void {
        $this->idDocument = $idDocument;
    }

    public function getNomFichier(): ?string {
        return $this->nomFichier;
    }

    public function setNomFichier(?string $nomFichier): void {
        $this->nomFichier = $nomFichier;
    }

    public function getChemin(): ?string {
        return $this->chemin;
    }

    public function setChemin(?string $chemin): void {
        $this->chemin = $chemin;
    }

    public function getTypeDocument(): ?string {
        return $this->typeDocument;
    }

    public function setTypeDocument(?string $typeDocument): void {
        $this->typeDocument = $typeDocument;
    }

    public function getDateUpload(): ?string {
        return $this->dateUpload;
    }

    public function setDateUpload(?string $dateUpload): void {
        $this->dateUpload = $dateUpload;
    }

    public function getIdRemboursement(): ?int {
        return $this->idRemboursement;
    }

    public function setIdRemboursement(?int $idRemboursement): void {
        $this->idRemboursement = $idRemboursement;
    }

    public function getIdBeneficiaire(): ?int {
        return $this->idBeneficiaire;
    }

    public function setIdBeneficiaire(?int $idBeneficiaire): void {
        $this->idBeneficiaire = $idBeneficiaire;
    }
}
