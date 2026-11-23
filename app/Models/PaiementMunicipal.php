<?php
declare(strict_types=1);

/** Entité métier correspondant à une ligne de municipalite_paiement. */
final class PaiementMunicipal
{
    public const STATUTS = ['en_attente', 'payee', 'annulee'];

    public function __construct(
        private ?int $idPaiement,
        private int $idDemande,
        private string $montant,
        private string $devise = 'TND',
        private string $statut = 'en_attente',
        private string $modePaiement = 'demo',
        private ?string $reference = null,
        private ?string $creeLe = null,
        private ?string $payeLe = null,
    ) {
        if (!in_array($this->statut, self::STATUTS, true)) {
            throw new InvalidArgumentException('Statut de paiement municipal invalide.');
        }
    }

    public static function fromRow(array $row): self
    {
        return new self(
            isset($row['id_paiement']) ? (int) $row['id_paiement'] : null,
            (int) $row['id_demande'],
            (string) $row['montant'],
            (string) ($row['devise'] ?? 'TND'),
            (string) ($row['statut'] ?? 'en_attente'),
            (string) ($row['mode_paiement'] ?? 'demo'),
            isset($row['reference']) ? (string) $row['reference'] : null,
            isset($row['cree_le']) ? (string) $row['cree_le'] : null,
            isset($row['paye_le']) ? (string) $row['paye_le'] : null,
        );
    }

    public function getIdPaiement(): ?int
    {
        return $this->idPaiement;
    }

    public function getIdDemande(): int
    {
        return $this->idDemande;
    }

    /** DECIMAL est conservé en chaîne pour éviter les erreurs d'arrondi. */
    public function getMontant(): string
    {
        return $this->montant;
    }

    public function getDevise(): string
    {
        return $this->devise;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getModePaiement(): string
    {
        return $this->modePaiement;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getCreeLe(): ?string
    {
        return $this->creeLe;
    }

    public function getPayeLe(): ?string
    {
        return $this->payeLe;
    }
}
