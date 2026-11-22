<?php

class Beneficiaire {
    private ?int $idBeneficiaire;
    private ?string $nom;
    private ?string $prenom;
    private ?string $dateNaissance;
    private ?string $lien;
    private ?string $statut;
    private ?int $idAssure;

    public function __construct(?int $idBeneficiaire, ?string $nom, ?string $prenom, ?string $dateNaissance, ?string $lien, ?string $statut, ?int $idAssure) {
        $this->idBeneficiaire = $idBeneficiaire;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->lien = $lien;
        $this->statut = $statut;
        $this->idAssure = $idAssure;
    }

    public function getIdBeneficiaire(): ?int {
        return $this->idBeneficiaire;
    }

    public function setIdBeneficiaire(?int $idBeneficiaire): void {
        $this->idBeneficiaire = $idBeneficiaire;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void {
        $this->prenom = $prenom;
    }

    public function getDateNaissance(): ?string {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?string $dateNaissance): void {
        $this->dateNaissance = $dateNaissance;
    }

    public function getLien(): ?string {
        return $this->lien;
    }

    public function setLien(?string $lien): void {
        $this->lien = $lien;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }

    public function getIdAssure(): ?int {
        return $this->idAssure;
    }

    public function setIdAssure(?int $idAssure): void {
        $this->idAssure = $idAssure;
    }
}
