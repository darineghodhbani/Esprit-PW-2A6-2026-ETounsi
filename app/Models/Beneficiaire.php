<?php

class Beneficiaire {
    private ?int $id;
    private ?int $idAssure;
    private ?string $nom;
    private ?string $prenom;
    private ?string $dateNaissance;
    private ?string $lienParente;
    private ?string $statutValidation;
    private ?int $soiUnique;

    public function __construct(?int $id, ?int $idAssure, ?string $nom, ?string $prenom, ?string $dateNaissance, ?string $lienParente, ?string $statutValidation, ?int $soiUnique) {
        $this->id = $id;
        $this->idAssure = $idAssure;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->lienParente = $lienParente;
        $this->statutValidation = $statutValidation;
        $this->soiUnique = $soiUnique;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdAssure(): ?int {
        return $this->idAssure;
    }

    public function setIdAssure(?int $idAssure): void {
        $this->idAssure = $idAssure;
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

    public function getLienParente(): ?string {
        return $this->lienParente;
    }

    public function setLienParente(?string $lienParente): void {
        $this->lienParente = $lienParente;
    }

    public function getStatutValidation(): ?string {
        return $this->statutValidation;
    }

    public function setStatutValidation(?string $statutValidation): void {
        $this->statutValidation = $statutValidation;
    }

    public function getSoiUnique(): ?int {
        return $this->soiUnique;
    }

    public function setSoiUnique(?int $soiUnique): void {
        $this->soiUnique = $soiUnique;
    }
}
