<?php

class LigneTransport {
    private ?int $idLigne;
    private ?string $numero;
    private ?string $nom;
    private ?float $tarif;
    private ?int $actif;

    public function __construct(?int $idLigne, ?string $numero, ?string $nom, ?float $tarif, ?int $actif) {
        $this->idLigne = $idLigne;
        $this->numero = $numero;
        $this->nom = $nom;
        $this->tarif = $tarif;
        $this->actif = $actif;
    }

    public function getIdLigne(): ?int {
        return $this->idLigne;
    }

    public function setIdLigne(?int $idLigne): void {
        $this->idLigne = $idLigne;
    }

    public function getNumero(): ?string {
        return $this->numero;
    }

    public function setNumero(?string $numero): void {
        $this->numero = $numero;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getTarif(): ?float {
        return $this->tarif;
    }

    public function setTarif(?float $tarif): void {
        $this->tarif = $tarif;
    }

    public function getActif(): ?int {
        return $this->actif;
    }

    public function setActif(?int $actif): void {
        $this->actif = $actif;
    }
}
