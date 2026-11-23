<?php
declare(strict_types=1);

/**
 * Document produit pour une demande municipale ; son fichier numérique est facultatif.
 */
final class DocumentMunicipal
{
    public const STATUTS = ['en_attente_demande', 'en_preparation', 'pret_a_retirer', 'delivre', 'rejete'];

    public function __construct(
        private ?int $idDocument,
        private int $idDemande,
        private string $typeDocument,
        private ?string $nomOriginal,
        private ?string $cheminStockage,
        private ?string $mimeType,
        private ?int $tailleOctets,
        private string $statut = 'en_attente_demande',
        private ?string $dateDepot = null,
    ) {
        if ($this->idDemande < 1 || trim($this->typeDocument) === '') {
            throw new InvalidArgumentException('Une pièce municipale doit être liée à une demande et avoir un type.');
        }
        if (!in_array($this->statut, self::STATUTS, true)) {
            throw new InvalidArgumentException('Statut de document municipal invalide.');
        }
        if ($this->tailleOctets !== null && $this->tailleOctets < 0) {
            throw new InvalidArgumentException('La taille du document ne peut pas être négative.');
        }
    }

    public static function fromRow(array $row): self
    {
        return new self(
            isset($row['id_document']) ? (int) $row['id_document'] : null,
            (int) $row['id_demande'],
            (string) $row['type_document'],
            isset($row['nom_original']) ? (string) $row['nom_original'] : null,
            isset($row['chemin_stockage']) ? (string) $row['chemin_stockage'] : null,
            isset($row['mime_type']) ? (string) $row['mime_type'] : null,
            isset($row['taille_octets']) ? (int) $row['taille_octets'] : null,
            (string) ($row['statut'] ?? 'en_attente_demande'),
            isset($row['date_depot']) ? (string) $row['date_depot'] : null,
        );
    }

    public function getIdDocument(): ?int
    {
        return $this->idDocument;
    }

    public function getIdDemande(): int
    {
        return $this->idDemande;
    }

    public function getTypeDocument(): string
    {
        return $this->typeDocument;
    }

    public function getNomOriginal(): ?string
    {
        return $this->nomOriginal;
    }

    public function getCheminStockage(): ?string
    {
        return $this->cheminStockage;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getTailleOctets(): ?int
    {
        return $this->tailleOctets;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getDateDepot(): ?string
    {
        return $this->dateDepot;
    }
}
