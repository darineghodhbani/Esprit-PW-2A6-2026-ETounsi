<?php
declare(strict_types=1);

/** Entité métier correspondant à une ligne de municipalite_notification. */
final class NotificationMunicipale
{
    public function __construct(
        private ?int $idNotification,
        private int $idCitoyen,
        private ?int $idDemande,
        private string $titre,
        private string $message,
        private bool $lue = false,
        private ?string $creeLe = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            isset($row['id_notification']) ? (int) $row['id_notification'] : null,
            (int) $row['id_citoyen'],
            isset($row['id_demande']) ? (int) $row['id_demande'] : null,
            (string) $row['titre'],
            (string) $row['message'],
            (bool) ($row['lu'] ?? false),
            isset($row['cree_le']) ? (string) $row['cree_le'] : null,
        );
    }

    public function getIdNotification(): ?int { return $this->idNotification; }
    public function getIdCitoyen(): int { return $this->idCitoyen; }
    public function getIdDemande(): ?int { return $this->idDemande; }
    public function getTitre(): string { return $this->titre; }
    public function getMessage(): string { return $this->message; }
    public function estLue(): bool { return $this->lue; }
    public function getCreeLe(): ?string { return $this->creeLe; }
}
