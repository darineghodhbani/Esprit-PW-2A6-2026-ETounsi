<?php

class Impot {
    private ?int $id;
    private ?int $idPropriete;
    private ?int $annee;
    private ?int $montant;
    private ?string $dateLimite;
    private ?string $statut;

    public function __construct(?int $id, ?int $idPropriete, ?int $annee, ?int $montant, ?string $dateLimite, ?string $statut) {
        $this->id = $id;
        $this->idPropriete = $idPropriete;
        $this->annee = $annee;
        $this->montant = $montant;
        $this->dateLimite = $dateLimite;
        $this->statut = $statut;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdPropriete(): ?int {
        return $this->idPropriete;
    }

    public function setIdPropriete(?int $idPropriete): void {
        $this->idPropriete = $idPropriete;
    }

    public function getAnnee(): ?int {
        return $this->annee;
    }

    public function setAnnee(?int $annee): void {
        $this->annee = $annee;
    }

    public function getMontant(): ?int {
        return $this->montant;
    }

    public function setMontant(?int $montant): void {
        $this->montant = $montant;
    }

    public function getDateLimite(): ?string {
        return $this->dateLimite;
    }

    public function setDateLimite(?string $dateLimite): void {
        $this->dateLimite = $dateLimite;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }
}
