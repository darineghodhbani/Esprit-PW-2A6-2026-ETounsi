<?php

class Horaire {
    public const SENS = ['Aller', 'Retour'];
    public const JOURS = ['Tous les jours', 'Lundi - Vendredi', 'Week-end'];

    private ?int $idHoraire;
    private ?int $idLigne;
    private ?string $sens;
    private ?string $heureDepart;
    private ?string $heureArrivee;
    private ?string $jours;

    public function __construct(?int $idHoraire, ?int $idLigne, ?string $sens, ?string $heureDepart, ?string $heureArrivee, ?string $jours) {
        $this->idHoraire = $idHoraire;
        $this->idLigne = $idLigne;
        $this->sens = $sens;
        $this->heureDepart = $heureDepart;
        $this->heureArrivee = $heureArrivee;
        $this->jours = $jours;
    }

    public function getIdHoraire(): ?int {
        return $this->idHoraire;
    }

    public function setIdHoraire(?int $idHoraire): void {
        $this->idHoraire = $idHoraire;
    }

    public function getIdLigne(): ?int {
        return $this->idLigne;
    }

    public function setIdLigne(?int $idLigne): void {
        $this->idLigne = $idLigne;
    }

    public function getSens(): ?string {
        return $this->sens;
    }

    public function setSens(?string $sens): void {
        $this->sens = $sens;
    }

    public function getHeureDepart(): ?string {
        return $this->heureDepart;
    }

    public function setHeureDepart(?string $heureDepart): void {
        $this->heureDepart = $heureDepart;
    }

    public function getHeureArrivee(): ?string {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(?string $heureArrivee): void {
        $this->heureArrivee = $heureArrivee;
    }

    public function getJours(): ?string {
        return $this->jours;
    }

    public function setJours(?string $jours): void {
        $this->jours = $jours;
    }
}
