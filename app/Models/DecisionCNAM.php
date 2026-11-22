<?php

class DecisionCNAM {
    private ?int $id;
    private ?int $idDemande;
    private ?int $idAgent;
    private ?string $resultat;
    private ?string $motif;
    private ?string $montantAccorde;
    private ?string $dateDecision;

    public function __construct(?int $id, ?int $idDemande, ?int $idAgent, ?string $resultat, ?string $motif, ?string $montantAccorde, ?string $dateDecision) {
        $this->id = $id;
        $this->idDemande = $idDemande;
        $this->idAgent = $idAgent;
        $this->resultat = $resultat;
        $this->motif = $motif;
        $this->montantAccorde = $montantAccorde;
        $this->dateDecision = $dateDecision;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getIdDemande(): ?int {
        return $this->idDemande;
    }

    public function setIdDemande(?int $idDemande): void {
        $this->idDemande = $idDemande;
    }

    public function getIdAgent(): ?int {
        return $this->idAgent;
    }

    public function setIdAgent(?int $idAgent): void {
        $this->idAgent = $idAgent;
    }

    public function getResultat(): ?string {
        return $this->resultat;
    }

    public function setResultat(?string $resultat): void {
        $this->resultat = $resultat;
    }

    public function getMotif(): ?string {
        return $this->motif;
    }

    public function setMotif(?string $motif): void {
        $this->motif = $motif;
    }

    public function getMontantAccorde(): ?string {
        return $this->montantAccorde;
    }

    public function setMontantAccorde(?string $montantAccorde): void {
        $this->montantAccorde = $montantAccorde;
    }

    public function getDateDecision(): ?string {
        return $this->dateDecision;
    }

    public function setDateDecision(?string $dateDecision): void {
        $this->dateDecision = $dateDecision;
    }
}
