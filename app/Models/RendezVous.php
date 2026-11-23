<?php
declare(strict_types=1);

/** Entité métier du rendez-vous demandé pour une démarche municipale. */
final class RendezVous
{
    public const STATUTS = ['en_attente', 'confirme', 'refuse', 'annule'];

    public function __construct(
        private ?int $idRendezVous,
        private int $idDemande,
        private string $gouvernorat,
        private string $delegation,
        private string $dateSouhaitee,
        private string $statut = 'en_attente',
    ) {
        if (!in_array($this->statut, self::STATUTS, true)) {
            throw new InvalidArgumentException('Statut de rendez-vous municipal invalide.');
        }
    }

    public static function fromRow(array $row): self
    {
        return new self(
            isset($row['id_rendez_vous']) ? (int) $row['id_rendez_vous'] : null,
            (int) $row['id_demande'],
            (string) ($row['gouvernorat'] ?? ''),
            (string) ($row['delegation'] ?? ''),
            (string) $row['date_rendez_vous'],
            (string) ($row['statut'] ?? 'en_attente'),
        );
    }

    public function getIdRendezVous(): ?int { return $this->idRendezVous; }
    public function getIdDemande(): int { return $this->idDemande; }
    public function getGouvernorat(): string { return $this->gouvernorat; }
    public function getDelegation(): string { return $this->delegation; }
    public function getDateSouhaitee(): string { return $this->dateSouhaitee; }
    public function getStatut(): string { return $this->statut; }
}
