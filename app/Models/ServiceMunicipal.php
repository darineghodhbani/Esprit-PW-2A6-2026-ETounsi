<?php

class ServiceMunicipal {
    private ?int $idService;
    private ?string $nom;
    private ?string $description;
    private ?int $delaiTraitement;

    public function __construct(?int $idService, ?string $nom, ?string $description, ?int $delaiTraitement) {
        $this->idService = $idService;
        $this->nom = $nom;
        $this->description = $description;
        $this->delaiTraitement = $delaiTraitement;
    }

    public function getIdService(): ?int {
        return $this->idService;
    }

    public function setIdService(?int $idService): void {
        $this->idService = $idService;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getDelaiTraitement(): ?int {
        return $this->delaiTraitement;
    }

    public function setDelaiTraitement(?int $delaiTraitement): void {
        $this->delaiTraitement = $delaiTraitement;
    }
}
