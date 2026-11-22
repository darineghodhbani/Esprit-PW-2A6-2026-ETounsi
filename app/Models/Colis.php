<?php

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
}
