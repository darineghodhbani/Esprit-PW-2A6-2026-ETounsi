<?php

class DemandeMunicipale {
    private ?int $idDemande;
    private ?string $objet;
    private ?string $description;
    private ?string $statut;
    private ?string $dateDemande;
    private ?int $idCitoyen;
    private ?int $idService;

    public function __construct(?int $idDemande, ?string $objet, ?string $description, ?string $statut, ?string $dateDemande, ?int $idCitoyen, ?int $idService) {
        $this->idDemande = $idDemande;
        $this->objet = $objet;
        $this->description = $description;
        $this->statut = $statut;
        $this->dateDemande = $dateDemande;
        $this->idCitoyen = $idCitoyen;
        $this->idService = $idService;
    }

    public function getIdDemande(): ?int {
        return $this->idDemande;
    }

    public function setIdDemande(?int $idDemande): void {
        $this->idDemande = $idDemande;
    }

    public function getObjet(): ?string {
        return $this->objet;
    }

    public function setObjet(?string $objet): void {
        $this->objet = $objet;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }

    public function getDateDemande(): ?string {
        return $this->dateDemande;
    }

    public function setDateDemande(?string $dateDemande): void {
        $this->dateDemande = $dateDemande;
    }

    public function getIdCitoyen(): ?int {
        return $this->idCitoyen;
    }

    public function setIdCitoyen(?int $idCitoyen): void {
        $this->idCitoyen = $idCitoyen;
    }

    public function getIdService(): ?int {
        return $this->idService;
    }

    public function setIdService(?int $idService): void {
        $this->idService = $idService;
    }
}
