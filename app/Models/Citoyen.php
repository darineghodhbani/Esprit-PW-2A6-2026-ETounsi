<?php

class Citoyen {
    private ?int $id;
    private ?string $nom;
    private ?string $email;
    private ?string $motDePasse;
    private ?string $role;
    private ?int $actif;
    private ?string $prenom;
    private ?string $telephone;
    private ?int $versionSession;

    public function __construct(?int $id, ?string $nom, ?string $email, ?string $motDePasse, ?string $role, ?int $actif, ?string $prenom, ?string $telephone, ?int $versionSession) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->actif = $actif;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->versionSession = $versionSession;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getMotDePasse(): ?string {
        return $this->motDePasse;
    }

    public function setMotDePasse(?string $motDePasse): void {
        $this->motDePasse = $motDePasse;
    }

    public function getRole(): ?string {
        return $this->role;
    }

    public function setRole(?string $role): void {
        $this->role = $role;
    }

    public function getActif(): ?int {
        return $this->actif;
    }

    public function setActif(?int $actif): void {
        $this->actif = $actif;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void {
        $this->prenom = $prenom;
    }

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void {
        $this->telephone = $telephone;
    }

    public function getVersionSession(): ?int {
        return $this->versionSession;
    }

    public function setVersionSession(?int $versionSession): void {
        $this->versionSession = $versionSession;
    }
}
