<?php

require_once __DIR__ . '/../../config.php';

class BureauPostal
{
    private $connexion;
    private $id;
    private $nom;
    private $adresse;
    private $ville;

    public function __construct($id = null, $nom = null, $adresse = null, $ville = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->connexion = Database::getConnection();
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

    public function listeBureaux()
    {
        $requete = $this->connexion->query('SELECT * FROM bureau_postal ORDER BY ville, nom');
        return $requete->fetchAll();
    }

    public function recupererBureau($id)
    {
        $requete = $this->connexion->prepare('SELECT * FROM bureau_postal WHERE id_bureau = :id');
        $requete->execute(['id' => $id]);
        $bureau = $requete->fetch();
        return $bureau === false ? null : $bureau;
    }

    public function ajouterBureau(BureauPostal $bureau)
    {
        $this->connexion->beginTransaction();
        try {
            $requete = $this->connexion->prepare(
                'INSERT INTO bureau_postal (nom, adresse, ville) VALUES (:nom, :adresse, :ville)'
            );
            $requete->execute([
                'nom' => $bureau->getNom(), 'adresse' => $bureau->getAdresse(), 'ville' => $bureau->getVille(),
            ]);
            $id = (int) $this->connexion->lastInsertId();
            $this->connexion->commit();
            return $id;
        } catch (Exception $erreur) {
            $this->connexion->rollBack();
            throw $erreur;
        }
    }

    public function modifierBureau(BureauPostal $bureau)
    {
        $this->connexion->beginTransaction();
        try {
            $requete = $this->connexion->prepare(
                'UPDATE bureau_postal SET nom = :nom, adresse = :adresse, ville = :ville WHERE id_bureau = :id'
            );
            $requete->execute([
                'id' => $bureau->getId(), 'nom' => $bureau->getNom(),
                'adresse' => $bureau->getAdresse(), 'ville' => $bureau->getVille(),
            ]);
            if ($requete->rowCount() === 0 && $this->recupererBureau($bureau->getId()) === null) {
                throw new OutOfBoundsException('Bureau de poste introuvable.');
            }
            $this->connexion->commit();
        } catch (Exception $erreur) {
            $this->connexion->rollBack();
            throw $erreur;
        }
    }

    public function deleteBureau($id)
    {
        $this->connexion->beginTransaction();
        try {
            $requete = $this->connexion->prepare(
                'DELETE FROM bureau_postal WHERE id_bureau = :id
                 AND id_bureau NOT IN (SELECT id_bureau_depart FROM colis)'
            );
            $requete->execute(['id' => $id]);
            $supprime = $requete->rowCount() === 1;
            $this->connexion->commit();
            return $supprime;
        } catch (Exception $erreur) {
            $this->connexion->rollBack();
            throw $erreur;
        }
    }
}
