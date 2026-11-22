<?php

class HabilitationCNAM {
    private ?int $idCitoyen;

    public function __construct(?int $idCitoyen) {
        $this->idCitoyen = $idCitoyen;
    }

    public function getIdCitoyen(): ?int {
        return $this->idCitoyen;
    }

    public function setIdCitoyen(?int $idCitoyen): void {
        $this->idCitoyen = $idCitoyen;
    }
}
