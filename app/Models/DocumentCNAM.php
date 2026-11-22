<?php

class DocumentCNAM {
    private ?int $id;
    private ?int $idBeneficiaire;
    private ?int $idDemande;
    private ?string $typeDocument;
    private ?string $fichier;
    private ?string $dateAjout;
    private ?string $nomOriginal;
    private ?string $mime;
    private ?int $taille;

    public function __construct(?int $id, ?int $idBeneficiaire, ?int $idDemande, ?string $typeDocument, ?string $fichier, ?string $dateAjout, ?string $nomOriginal, ?string $mime, ?int $taille) {
        $this->id = $id;
        $this->idBeneficiaire = $idBeneficiaire;
        $this->idDemande = $idDemande;
        $this->typeDocument = $typeDocument;
        $this->fichier = $fichier;
        $this->dateAjout = $dateAjout;
        $this->nomOriginal = $nomOriginal;
        $this->mime = $mime;
        $this->taille = $taille;
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

    public function getIdDemande(): ?int {
        return $this->idDemande;
    }

    public function setIdDemande(?int $idDemande): void {
        $this->idDemande = $idDemande;
    }

    public function getTypeDocument(): ?string {
        return $this->typeDocument;
    }

    public function setTypeDocument(?string $typeDocument): void {
        $this->typeDocument = $typeDocument;
    }

    public function getFichier(): ?string {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): void {
        $this->fichier = $fichier;
    }

    public function getDateAjout(): ?string {
        return $this->dateAjout;
    }

    public function setDateAjout(?string $dateAjout): void {
        $this->dateAjout = $dateAjout;
    }

    public function getNomOriginal(): ?string {
        return $this->nomOriginal;
    }

    public function setNomOriginal(?string $nomOriginal): void {
        $this->nomOriginal = $nomOriginal;
    }

    public function getMime(): ?string {
        return $this->mime;
    }

    public function setMime(?string $mime): void {
        $this->mime = $mime;
    }

    public function getTaille(): ?int {
        return $this->taille;
    }

    public function setTaille(?int $taille): void {
        $this->taille = $taille;
    }
}
