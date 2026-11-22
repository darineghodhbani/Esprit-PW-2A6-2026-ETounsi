<?php

require_once __DIR__ . '/../../config.php';

class Colis
{
    public const STATUTS = ['En attente', 'Accepté', 'En transit', 'Livré', 'Annulé'];

    private const SELECT_COLIS =
        'SELECT c.id_colis, c.id_bureau_depart, c.id_citoyen, c.numero_suivi,
                c.statut, c.created_at, c.nom_citoyen, c.date_declaration, b.nom AS bureau, b.ville
         FROM colis c INNER JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau';


    private $connexion;
    private $id;
    private $idBureau;
    private $idCitoyen;
    private $numeroSuivi;
    private $statut;

    public function __construct($id = null, $idBureau = null, $idCitoyen = null, $numeroSuivi = null, $statut = null)
    {
        $this->id = $id;
        $this->idBureau = $idBureau;
        $this->idCitoyen = $idCitoyen;
        $this->numeroSuivi = $numeroSuivi;
        $this->statut = $statut;
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

    public function listeCitoyens()
    {
        $requete = $this->connexion->query(
            "SELECT id_citoyen, nom FROM citoyen WHERE role = 'citoyen' AND actif = 1 ORDER BY nom"
        );
        return $requete->fetchAll();
    }

    public function listeColis($idCitoyen = null)
    {
        $tri = ' ORDER BY c.created_at DESC, c.id_colis DESC';
        if ($idCitoyen === null) {
            $requete = $this->connexion->query(self::SELECT_COLIS . $tri);
        } else {
            $requete = $this->connexion->prepare(
                self::SELECT_COLIS . ' WHERE c.id_citoyen = :id_citoyen' . $tri
            );
            $requete->execute(['id_citoyen' => $idCitoyen]);
        }
        return $requete->fetchAll();
    }

    public function recupererColis($id)
    {
        $requete = $this->connexion->prepare(self::SELECT_COLIS . ' WHERE c.id_colis = :id');
        $requete->execute(['id' => $id]);
        $colis = $requete->fetch();
        if ($colis === false) {
            return null;
        }
        return $colis;
    }

    public function chercherColis($numero)
    {
        $requete = $this->connexion->prepare(self::SELECT_COLIS . ' WHERE c.numero_suivi = :numero');
        $requete->execute(['numero' => strtoupper(trim($numero))]);
        $colis = $requete->fetch();
        if ($colis === false) {
            return null;
        }
        return $colis;
    }

    public function recupererColisCitoyen($id, $idCitoyen)
    {
        $requete = $this->connexion->prepare(
            self::SELECT_COLIS . ' WHERE c.id_colis = :id AND c.id_citoyen = :id_citoyen'
        );
        $requete->execute(['id' => $id, 'id_citoyen' => $idCitoyen]);
        $colis = $requete->fetch();
        return $colis === false ? null : $colis;
    }

    public function modifierDeclaration($id, $idCitoyen, $idBureau, $nomCitoyen, $dateDeclaration)
    {
        // Le propriétaire et le statut sont aussi vérifiés au moment de l’écriture.
        $requete = $this->connexion->prepare(
            'UPDATE colis SET id_bureau_depart = :id_bureau, nom_citoyen = :nom, date_declaration = :date_declaration
             WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut'
        );
        $requete->execute([
            'id_bureau' => $idBureau, 'id' => $id,
            'id_citoyen' => $idCitoyen, 'statut' => 'En attente',
            'nom' => $nomCitoyen, 'date_declaration' => $dateDeclaration,
        ]);
        return $requete->rowCount() === 1;
    }

    public function supprimerDeclaration($id, $idCitoyen)
    {
        $requete = $this->connexion->prepare(
            'DELETE FROM colis WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut'
        );
        $requete->execute(['id' => $id, 'id_citoyen' => $idCitoyen, 'statut' => 'En attente']);
        return $requete->rowCount() === 1;
    }

    public function ajouterColis($idBureau, $idCitoyen, $nomCitoyen = null, $dateDeclaration = null)
    {
        // Les créations dans le Back Office reprennent le compte sélectionné par l’admin.
        if ($nomCitoyen === null) {
            $compte = $this->connexion->prepare('SELECT nom, prenom FROM citoyen WHERE id_citoyen = :id');
            $compte->execute(['id' => $idCitoyen]);
            $citoyen = $compte->fetch();
            $nomCitoyen = trim(($citoyen['prenom'] ?? '') . ' ' . ($citoyen['nom'] ?? ''));
        }
        if ($dateDeclaration === null) $dateDeclaration = date('Y-m-d');
        $requete = $this->connexion->prepare(
            'INSERT INTO colis (id_bureau_depart, id_citoyen, numero_suivi, statut, nom_citoyen, date_declaration)
             VALUES (:id_bureau, :id_citoyen, :numero, :statut, :nom, :date_declaration)'
        );

        $this->connexion->beginTransaction();
        try {
            $enregistre = false;
            while (!$enregistre) {
                $numero = 'ET' . strtoupper(bin2hex(random_bytes(5))) . 'TN';
                $colis = new Colis(null, $idBureau, $idCitoyen, $numero, 'En attente');
                try {
                    $requete->execute([
                        'id_bureau' => $colis->getIdBureau(),
                        'id_citoyen' => $colis->getIdCitoyen(),
                        'numero' => $colis->getNumeroSuivi(),
                        'statut' => $colis->getStatut(),
                        'nom' => $nomCitoyen,
                        'date_declaration' => $dateDeclaration,
                    ]);
                    $enregistre = true;
                } catch (PDOException $erreur) {
                    // Un numéro déjà attribué est remplacé par un nouveau numéro.
                    if (!isset($erreur->errorInfo[1]) || $erreur->errorInfo[1] !== 1062) {
                        throw $erreur;
                    }
                }
            }

            $colis = $this->recupererColis((int) $this->connexion->lastInsertId());
            $this->connexion->commit();
            return $colis;
        } catch (Exception $erreur) {
            $this->connexion->rollBack();
            throw $erreur;
        }
    }

    public function modifierColis($id, $statut, $statutActuel)
    {
        if (!in_array($statut, self::STATUTS, true)) {
            throw new InvalidArgumentException('Statut de colis invalide.');
        }
        if (!in_array($statut, self::statutsSuivants($statutActuel), true)) {
            return false;
        }
        // Une déclaration annulée ou déjà traitée ne peut pas être écrasée.
        $this->connexion->beginTransaction();
        try {
            $requete = $this->connexion->prepare(
                'UPDATE colis SET statut = :statut WHERE id_colis = :id AND statut = :statut_actuel'
            );
            $requete->execute(['statut' => $statut, 'id' => $id, 'statut_actuel' => $statutActuel]);
            $modifie = $requete->rowCount() === 1;
            $this->connexion->commit();
            return $modifie;
        } catch (Exception $erreur) {
            $this->connexion->rollBack();
            throw $erreur;
        }
    }

    public function annulerColis($id, $idCitoyen)
    {
        $this->connexion->beginTransaction();
        try {
            $requete = $this->connexion->prepare(
                'UPDATE colis SET statut = :statut_annule
                 WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut_initial'
            );
            $requete->execute([
                'statut_annule' => 'Annulé',
                'id' => $id,
                'id_citoyen' => $idCitoyen,
                'statut_initial' => 'En attente',
            ]);
            $annule = $requete->rowCount() === 1;
            $this->connexion->commit();
            return $annule;
        } catch (Exception $erreur) {
            $this->connexion->rollBack();
            throw $erreur;
        }
    }

    public function deleteColis($id)
    {
        $this->connexion->beginTransaction();
        try {
            $requete = $this->connexion->prepare('DELETE FROM colis WHERE id_colis = :id');
            $requete->execute(['id' => $id]);
            $supprime = $requete->rowCount() === 1;
            $this->connexion->commit();
            return $supprime;
        } catch (Exception $erreur) {
            $this->connexion->rollBack();
            throw $erreur;
        }
    }

    public static function statutsSuivants($statut)
    {
        $transitions = [
            'En attente' => ['Accepté', 'Annulé'],
            'Accepté' => ['En transit', 'Annulé'],
            'En transit' => ['Livré'],
        ];
        return $transitions[$statut] ?? [];
    }
}
