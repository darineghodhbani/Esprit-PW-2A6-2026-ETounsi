<?php

class AssureSocial {
    private ?int $id;
    private ?int $idCitoyen;
    private ?string $matriculeCnam;
    private ?string $filiereSoins;
    private ?string $telephone;
    private ?string $adresse;

    public function __construct(?int $id, ?int $idCitoyen, ?string $matriculeCnam, ?string $filiereSoins, ?string $telephone, ?string $adresse) {
        $this->id = $id;
        $this->idCitoyen = $idCitoyen;
        $this->matriculeCnam = $matriculeCnam;
        $this->filiereSoins = $filiereSoins;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
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

    public function getMatriculeCnam(): ?string {
        return $this->matriculeCnam;
    }

    public function setMatriculeCnam(?string $matriculeCnam): void {
        $this->matriculeCnam = $matriculeCnam;
    }

    public function getFiliereSoins(): ?string {
        return $this->filiereSoins;
    }

    public function setFiliereSoins(?string $filiereSoins): void {
        $this->filiereSoins = $filiereSoins;
    }

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void {
        $this->telephone = $telephone;
    }

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void {
        $this->adresse = $adresse;
    }
}
