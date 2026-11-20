<?php

class Propriete
{
    private int $id = 0;
    private int $idUser = 0;
    private string $type = '';
    private string $reference = '';
    private string $adresse = '';
    private int $valeur = 0;
    private string $createdAt = '';
    private string $proprietaire = '';

    public function __construct() {}

    public function getId(): int              { return $this->id; }
    public function getIdUser(): int          { return $this->idUser; }
    public function getType(): string         { return $this->type; }
    public function getReference(): string    { return $this->reference; }
    public function getAdresse(): string      { return $this->adresse; }
    public function getValeur(): int          { return $this->valeur; }
    public function getCreatedAt(): string    { return $this->createdAt; }
    public function getProprietaire(): string { return $this->proprietaire; }

    public function setId(int $id): void               { $this->id = $id; }
    public function setIdUser(int $idUser): void       { $this->idUser = $idUser; }
    public function setType(string $type): void        { $this->type = $type; }
    public function setReference(string $ref): void    { $this->reference = $ref; }
    public function setAdresse(string $adresse): void  { $this->adresse = $adresse; }
    public function setValeur(int $valeur): void       { $this->valeur = $valeur; }
    public function setCreatedAt(string $date): void   { $this->createdAt = $date; }
    public function setProprietaire(string $nom): void { $this->proprietaire = $nom; }

    public static function fromArray(array $row): self
    {
        $p = new self();
        $p->setId((int) ($row['id'] ?? 0));
        $p->setIdUser((int) ($row['id_user'] ?? 0));
        $p->setType((string) ($row['type'] ?? ''));
        $p->setReference((string) ($row['reference'] ?? ''));
        $p->setAdresse((string) ($row['adresse'] ?? ''));
        $p->setValeur((int) ($row['valeur'] ?? 0));
        $p->setCreatedAt((string) ($row['created_at'] ?? ''));
        $p->setProprietaire((string) ($row['proprietaire'] ?? ''));
        return $p;
    }
}
