<?php

class Colis
{
    private $id;
    private $idBureau;
    private $idCitoyen;
    private $numeroSuivi;
    private $statut;

    public function __construct($id, $idBureau, $idCitoyen, $numeroSuivi, $statut)
    {
        $this->id = $id;
        $this->idBureau = $idBureau;
        $this->idCitoyen = $idCitoyen;
        $this->numeroSuivi = $numeroSuivi;
        $this->statut = $statut;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdBureau()
    {
        return $this->idBureau;
    }

    public function setIdBureau($idBureau)
    {
        $this->idBureau = $idBureau;
    }

    public function getIdCitoyen()
    {
        return $this->idCitoyen;
    }

    public function setIdCitoyen($idCitoyen)
    {
        $this->idCitoyen = $idCitoyen;
    }

    public function getNumeroSuivi()
    {
        return $this->numeroSuivi;
    }

    public function setNumeroSuivi($numeroSuivi)
    {
        $this->numeroSuivi = $numeroSuivi;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }
}
