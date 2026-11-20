<?php

class User
{
    private int $id = 0;
    private string $nom = '';
    private string $prenom = '';
    private string $email = '';
    private string $telephone = '';
    private string $password = '';
    private string $role = 'user';
    private string $statut = 'actif';
    private string $createdAt = '';

    public function __construct() {}

    public function getId(): int            { return $this->id; }
    public function getNom(): string        { return $this->nom; }
    public function getPrenom(): string     { return $this->prenom; }
    public function getNomComplet(): string { return $this->prenom . ' ' . $this->nom; }
    public function getEmail(): string      { return $this->email; }
    public function getTelephone(): string  { return $this->telephone; }
    public function getPassword(): string   { return $this->password; }
    public function getRole(): string       { return $this->role; }
    public function getStatut(): string     { return $this->statut; }
    public function getCreatedAt(): string  { return $this->createdAt; }

    public function setId(int $id): void                { $this->id = $id; }
    public function setNom(string $nom): void           { $this->nom = $nom; }
    public function setPrenom(string $prenom): void     { $this->prenom = $prenom; }
    public function setEmail(string $email): void       { $this->email = $email; }
    public function setTelephone(string $tel): void     { $this->telephone = $tel; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setRole(string $role): void         { $this->role = $role; }
    public function setStatut(string $statut): void     { $this->statut = $statut; }
    public function setCreatedAt(string $date): void    { $this->createdAt = $date; }

    public static function fromArray(array $row): self
    {
        $user = new self();
        $user->setId((int) ($row['id'] ?? 0));
        $user->setNom((string) ($row['nom'] ?? ''));
        $user->setPrenom((string) ($row['prenom'] ?? ''));
        $user->setEmail((string) ($row['email'] ?? ''));
        $user->setTelephone((string) ($row['telephone'] ?? ''));
        $user->setPassword((string) ($row['password'] ?? ''));
        $user->setRole((string) ($row['role'] ?? 'user'));
        $user->setStatut((string) ($row['statut'] ?? 'actif'));
        $user->setCreatedAt((string) ($row['created_at'] ?? ''));
        return $user;
    }
}
