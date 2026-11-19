<?php

class Utilisateur
{
    private $id;
    private $nom;
    private $email;
    private $motDePasse;
    private $role;
    private $actif;
    private $versionSession;
    private $createdAt;

    public function getId()
    {
        return $this->id;
    }
    public function getNom()
    {
        return $this->nom;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getMotDePasse()
    {
        return $this->motDePasse;
    }
    public function getRole()
    {
        return $this->role;
    }
    public function getActif()
    {
        return $this->actif;
    }
    public function getVersionSession()
    {
        return $this->versionSession;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }
    public function setNom($nom)
    {
        $this->nom = $nom;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function setMotDePasse($motDePasse)
    {
        $this->motDePasse = $motDePasse;
    }
    public function setRole($role)
    {
        $this->role = $role;
    }
    public function setActif($actif)
    {
        $this->actif = (int) $actif;
    }
    public function setVersionSession($version)
    {
        $this->versionSession = (int) $version;
    }
    public function setCreatedAt($date)
    {
        $this->createdAt = $date;
    }

    public static function depuisTableau($ligne)
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setId($ligne['id']);
        $utilisateur->setNom($ligne['nom']);
        $utilisateur->setEmail($ligne['email']);
        $utilisateur->setMotDePasse($ligne['mot_de_passe'] ?? null);
        $utilisateur->setRole($ligne['role']);
        $utilisateur->setActif($ligne['actif']);
        $utilisateur->setVersionSession($ligne['version_session']);
        $utilisateur->setCreatedAt($ligne['created_at']);
        return $utilisateur;
    }

    public function versTableau()
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'email' => $this->email,
            'role' => $this->role,
            'actif' => $this->actif,
            'version_session' => $this->versionSession,
            'created_at' => $this->createdAt,
        ];
    }
}
