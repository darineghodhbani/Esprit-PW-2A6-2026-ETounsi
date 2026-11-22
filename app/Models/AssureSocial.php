<?php

class AssureSocial {
    private ?int $idAssure;
    private ?string $numeroAssure;
    private ?string $cin;
    private ?string $regime;
    private ?string $dateAffiliation;
    private ?int $idCitoyen;

    public function __construct(?int $idAssure, ?string $numeroAssure, ?string $cin, ?string $regime, ?string $dateAffiliation, ?int $idCitoyen) {
        $this->idAssure = $idAssure;
        $this->numeroAssure = $numeroAssure;
        $this->cin = $cin;
        $this->regime = $regime;
        $this->dateAffiliation = $dateAffiliation;
        $this->idCitoyen = $idCitoyen;
    }

    public function getIdAssure(): ?int {
        return $this->idAssure;
    }

    public function setIdAssure(?int $idAssure): void {
        $this->idAssure = $idAssure;
    }

    public function getNumeroAssure(): ?string {
        return $this->numeroAssure;
    }

    public function setNumeroAssure(?string $numeroAssure): void {
        $this->numeroAssure = $numeroAssure;
    }

    public function getCin(): ?string {
        return $this->cin;
    }

    public function setCin(?string $cin): void {
        $this->cin = $cin;
    }

    public function getRegime(): ?string {
        return $this->regime;
    }

    public function setRegime(?string $regime): void {
        $this->regime = $regime;
    }

    public function getDateAffiliation(): ?string {
        return $this->dateAffiliation;
    }

    public function setDateAffiliation(?string $dateAffiliation): void {
        $this->dateAffiliation = $dateAffiliation;
    }

    public function getIdCitoyen(): ?int {
        return $this->idCitoyen;
    }

    public function setIdCitoyen(?int $idCitoyen): void {
        $this->idCitoyen = $idCitoyen;
    }
}
