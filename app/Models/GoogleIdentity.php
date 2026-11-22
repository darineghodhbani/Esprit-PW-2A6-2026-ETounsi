<?php

class GoogleIdentity {
    private ?string $googleSub;
    private ?int $idCitoyen;
    private ?string $dateLiaison;

    public function __construct(?string $googleSub, ?int $idCitoyen, ?string $dateLiaison) {
        $this->googleSub = $googleSub;
        $this->idCitoyen = $idCitoyen;
        $this->dateLiaison = $dateLiaison;
    }

    public function getGoogleSub(): ?string {
        return $this->googleSub;
    }

    public function setGoogleSub(?string $googleSub): void {
        $this->googleSub = $googleSub;
    }

    public function getIdCitoyen(): ?int {
        return $this->idCitoyen;
    }

    public function setIdCitoyen(?int $idCitoyen): void {
        $this->idCitoyen = $idCitoyen;
    }

    public function getDateLiaison(): ?string {
        return $this->dateLiaison;
    }

    public function setDateLiaison(?string $dateLiaison): void {
        $this->dateLiaison = $dateLiaison;
    }
}
