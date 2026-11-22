<?php

class Station {
    private ?int $idStation;
    private ?string $nom;
    private ?string $adresse;
    private ?int $ordre;
    private ?int $idLigne;

    public function __construct(?int $idStation, ?string $nom, ?string $adresse, ?int $ordre, ?int $idLigne) {
        $this->idStation = $idStation;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->ordre = $ordre;
        $this->idLigne = $idLigne;
    }

    public function getIdStation(): ?int {
        return $this->idStation;
    }

    public function setIdStation(?int $idStation): void {
        $this->idStation = $idStation;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void {
        $this->adresse = $adresse;
    }

    public function getOrdre(): ?int {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): void {
        $this->ordre = $ordre;
    }

    public function getIdLigne(): ?int {
        return $this->idLigne;
    }

    public function setIdLigne(?int $idLigne): void {
        $this->idLigne = $idLigne;
    }
}
