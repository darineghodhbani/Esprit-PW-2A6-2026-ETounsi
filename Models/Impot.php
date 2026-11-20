<?php

class Impot
{
    private int $id = 0;
    private int $idPropriete = 0;
    private int $annee = 0;
    private int $montant = 0;
    private string $dateLimite = '';
    private string $statut = 'non_paye';
    private string $createdAt = '';
    private string $reference = '';

    public function __construct() {}

    public function getId(): int            { return $this->id; }
    public function getIdPropriete(): int   { return $this->idPropriete; }
    public function getAnnee(): int         { return $this->annee; }
    public function getMontant(): int       { return $this->montant; }
    public function getDateLimite(): string { return $this->dateLimite; }
    public function getStatut(): string     { return $this->statut; }
    public function getCreatedAt(): string  { return $this->createdAt; }
    public function getReference(): string  { return $this->reference; }

    public function setId(int $id): void              { $this->id = $id; }
    public function setIdPropriete(int $id): void     { $this->idPropriete = $id; }
    public function setAnnee(int $annee): void        { $this->annee = $annee; }
    public function setMontant(int $montant): void    { $this->montant = $montant; }
    public function setDateLimite(string $date): void { $this->dateLimite = $date; }
    public function setStatut(string $statut): void   { $this->statut = $statut; }
    public function setCreatedAt(string $date): void  { $this->createdAt = $date; }
    public function setReference(string $ref): void   { $this->reference = $ref; }

    public static function fromArray(array $row): self
    {
        $i = new self();
        $i->setId((int) ($row['id'] ?? 0));
        $i->setIdPropriete((int) ($row['id_propriete'] ?? 0));
        $i->setAnnee((int) ($row['annee'] ?? 0));
        $i->setMontant((int) ($row['montant'] ?? 0));
        $i->setDateLimite((string) ($row['date_limite'] ?? ''));
        $i->setStatut((string) ($row['statut'] ?? 'non_paye'));
        $i->setCreatedAt((string) ($row['created_at'] ?? ''));
        $i->setReference((string) ($row['reference'] ?? ''));
        return $i;
    }
}
