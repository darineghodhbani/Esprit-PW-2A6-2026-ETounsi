<?php

class BureauPostal
{
    private $id;
    private $nom;
    private $adresse;
    private $ville;

    public function __construct($id, $nom, $adresse, $ville)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->ville = $ville;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
    }
}
