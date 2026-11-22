<?php
declare(strict_types=1);

/** Entité métier d'une demande citoyenne du module Municipalité. */
final class DemandeMunicipale
{
    public function __construct(
        private int $serviceId,
        private int $citoyenId,
        private string $telephone,
        private ?string $nomConcerne,
        private ?string $dateActe,
        private ?string $lieuActe,
        private string $detailsJson,
        private ?string $gouvernorat = null,
        private ?string $delegation = null,
        private ?string $dateSouhaitee = null,
    ) {}

    public function getServiceId(): int { return $this->serviceId; }
    public function getCitoyenId(): int { return $this->citoyenId; }
    public function getTelephone(): string { return $this->telephone; }
    public function getNomConcerne(): ?string { return $this->nomConcerne; }
    public function getDateActe(): ?string { return $this->dateActe; }
    public function getLieuActe(): ?string { return $this->lieuActe; }
    public function getDetailsJson(): string { return $this->detailsJson; }
    public function getGouvernorat(): ?string { return $this->gouvernorat; }
    public function getDelegation(): ?string { return $this->delegation; }
    public function getDateSouhaitee(): ?string { return $this->dateSouhaitee; }
}
