<?php

class Propriete {
    private ?int $id;
    private ?int $idCitoyen;
    private ?string $type;
    private ?string $reference;
    private ?string $adresse;
    private ?int $valeur;

    public function __construct(?int $id, ?int $idCitoyen, ?string $type, ?string $reference, ?string $adresse, ?int $valeur) {
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
}
