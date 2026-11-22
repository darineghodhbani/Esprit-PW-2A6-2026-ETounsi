<?php

class DecisionCNAM {
    private ?int $idDecision;
    private ?string $decision;
    private ?string $motif;
    private ?float $montantAccorde;
    private ?string $dateDecision;
    private ?int $idRemboursement;
    private ?int $idAgent;

    public function __construct(?int $idDecision, ?string $decision, ?string $motif, ?float $montantAccorde, ?string $dateDecision, ?int $idRemboursement, ?int $idAgent) {
        $this->idDecision = $idDecision;
        $this->decision = $decision;
        $this->motif = $motif;
        $this->montantAccorde = $montantAccorde;
        $this->dateDecision = $dateDecision;
        $this->idRemboursement = $idRemboursement;
        $this->idAgent = $idAgent;
    }

    public function getIdDecision(): ?int {
        return $this->idDecision;
    }

    public function setIdDecision(?int $idDecision): void {
        $this->idDecision = $idDecision;
    }

    public function getDecision(): ?string {
        return $this->decision;
    }

    public function setDecision(?string $decision): void {
        $this->decision = $decision;
    }

    public function getMotif(): ?string {
        return $this->motif;
    }

    public function setMotif(?string $motif): void {
        $this->motif = $motif;
    }

    public function getMontantAccorde(): ?float {
        return $this->montantAccorde;
    }

    public function setMontantAccorde(?float $montantAccorde): void {
        $this->montantAccorde = $montantAccorde;
    }

    public function getDateDecision(): ?string {
        return $this->dateDecision;
    }

    public function setDateDecision(?string $dateDecision): void {
        $this->dateDecision = $dateDecision;
    }

    public function getIdRemboursement(): ?int {
        return $this->idRemboursement;
    }

    public function setIdRemboursement(?int $idRemboursement): void {
        $this->idRemboursement = $idRemboursement;
    }

    public function getIdAgent(): ?int {
        return $this->idAgent;
    }

    public function setIdAgent(?int $idAgent): void {
        $this->idAgent = $idAgent;
    }
}
