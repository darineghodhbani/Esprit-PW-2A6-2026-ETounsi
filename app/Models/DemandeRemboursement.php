<?php

class DemandeRemboursement {
    private ?int $idRemboursement;
    private ?string $typeSoin;
    private ?float $montant;
    private ?string $statut;
    private ?string $dateDemande;
    private ?int $idAssure;

    public function __construct(?int $idRemboursement, ?string $typeSoin, ?float $montant, ?string $statut, ?string $dateDemande, ?int $idAssure) {
        $this->idRemboursement = $idRemboursement;
        $this->typeSoin = $typeSoin;
        $this->montant = $montant;
        $this->statut = $statut;
        $this->dateDemande = $dateDemande;
        $this->idAssure = $idAssure;
    }

    public function getIdRemboursement(): ?int {
        return $this->idRemboursement;
    }

    public function setIdRemboursement(?int $idRemboursement): void {
        $this->idRemboursement = $idRemboursement;
    }

    public function getTypeSoin(): ?string {
        return $this->typeSoin;
    }

    public function setTypeSoin(?string $typeSoin): void {
        $this->typeSoin = $typeSoin;
    }

    public function getMontant(): ?float {
        return $this->montant;
    }

    public function setMontant(?float $montant): void {
        $this->montant = $montant;
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

    public function getIdAssure(): ?int {
        return $this->idAssure;
    }

    public function setIdAssure(?int $idAssure): void {
        $this->idAssure = $idAssure;
    }
}
