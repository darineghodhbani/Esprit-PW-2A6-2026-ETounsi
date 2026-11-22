<?php

class AuditLog {
    private ?int $id;
    private ?int $idUtilisateur;
    private ?string $nomAuteur;
    private ?string $action;
    private ?int $idCible;
    private ?string $details;
    private ?string $adresseIp;
    private ?string $dateCreation;

    public function __construct(?int $id, ?int $idUtilisateur, ?string $nomAuteur, ?string $action, ?int $idCible, ?string $details, ?string $adresseIp, ?string $dateCreation) {
        $this->id = $id;
        $this->idUtilisateur = $idUtilisateur;
        $this->nomAuteur = $nomAuteur;
        $this->action = $action;
        $this->idCible = $idCible;
        $this->details = $details;
        $this->adresseIp = $adresseIp;
        $this->dateCreation = $dateCreation;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdUtilisateur(): ?int {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getNomAuteur(): ?string {
        return $this->nomAuteur;
    }

    public function setNomAuteur(?string $nomAuteur): void {
        $this->nomAuteur = $nomAuteur;
    }

    public function getAction(): ?string {
        return $this->action;
    }

    public function setAction(?string $action): void {
        $this->action = $action;
    }

    public function getIdCible(): ?int {
        return $this->idCible;
    }

    public function setIdCible(?int $idCible): void {
        $this->idCible = $idCible;
    }

    public function getDetails(): ?string {
        return $this->details;
    }

    public function setDetails(?string $details): void {
        $this->details = $details;
    }

    public function getAdresseIp(): ?string {
        return $this->adresseIp;
    }

    public function setAdresseIp(?string $adresseIp): void {
        $this->adresseIp = $adresseIp;
    }

    public function getDateCreation(): ?string {
        return $this->dateCreation;
    }

    public function setDateCreation(?string $dateCreation): void {
        $this->dateCreation = $dateCreation;
    }
}
