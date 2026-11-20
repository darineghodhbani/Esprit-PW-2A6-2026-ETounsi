<?php

require_once __DIR__ . '/../models/BureauPostal.php';
require_once __DIR__ . '/../models/Colis.php';

class PosteController
{
    public const STATUSES = ['En attente', 'Accepté', 'En transit', 'Livré', 'Annulé'];

    private const SELECT_COLIS =
        'SELECT c.id_colis, c.id_bureau_depart, c.id_citoyen, c.numero_suivi,
                c.statut, c.created_at, b.nom AS bureau, b.ville
         FROM Colis c INNER JOIN BureauPostal b ON c.id_bureau_depart = b.id_bureau';

    private $connexion;

    public function __construct($connexion)
    {
        $this->connexion = $connexion;
    }

    public function listCitizens()
    {
        $requete = $this->connexion->query(
            "SELECT id_citoyen, nom FROM Citoyen WHERE role = 'citoyen' AND actif = 1 ORDER BY nom"
        );
        return $requete->fetchAll();
    }

    public function listOffices()
    {
        $requete = $this->connexion->query('SELECT * FROM BureauPostal ORDER BY ville, nom');
        return $requete->fetchAll();
    }

    public function findOffice($id)
    {
        $requete = $this->connexion->prepare('SELECT * FROM BureauPostal WHERE id_bureau = :id');
        $requete->execute(['id' => $id]);
        $bureau = $requete->fetch();
        if ($bureau === false) {
            return null;
        }
        return $bureau;
    }

    public function saveOffice($id, $nom, $adresse, $ville)
    {
        $bureau = new BureauPostal($id, $nom, $adresse, $ville);
        $valeurs = [
            'nom' => $bureau->getNom(),
            'adresse' => $bureau->getAdresse(),
            'ville' => $bureau->getVille(),
        ];
        if ($id === null) {
            $requete = $this->connexion->prepare(
                'INSERT INTO BureauPostal (nom, adresse, ville) VALUES (:nom, :adresse, :ville)'
            );
            $requete->execute($valeurs);
            return (int) $this->connexion->lastInsertId();
        }

        $requete = $this->connexion->prepare(
            'UPDATE BureauPostal SET nom = :nom, adresse = :adresse, ville = :ville WHERE id_bureau = :id'
        );
        $valeurs['id'] = $id;
        $requete->execute($valeurs);
        if ($requete->rowCount() === 0 && $this->findOffice($id) === null) {
            throw new OutOfBoundsException('Bureau de poste introuvable.');
        }
        return $id;
    }

    public function deleteOffice($id)
    {
        $requete = $this->connexion->prepare(
            'DELETE FROM BureauPostal WHERE id_bureau = :id
             AND id_bureau NOT IN (SELECT id_bureau_depart FROM Colis)'
        );
        $requete->execute(['id' => $id]);
        return $requete->rowCount() === 1;
    }

    public function listParcels($idCitoyen = null)
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

    public function findParcel($id)
    {
        $requete = $this->connexion->prepare(self::SELECT_COLIS . ' WHERE c.id_colis = :id');
        $requete->execute(['id' => $id]);
        $colis = $requete->fetch();
        if ($colis === false) {
            return null;
        }
        return $colis;
    }

    public function track($numero)
    {
        $requete = $this->connexion->prepare(self::SELECT_COLIS . ' WHERE c.numero_suivi = :numero');
        $requete->execute(['numero' => strtoupper(trim($numero))]);
        $colis = $requete->fetch();
        if ($colis === false) {
            return null;
        }
        return $colis;
    }

    public function createParcel($idBureau, $idCitoyen)
    {
        $requete = $this->connexion->prepare(
            'INSERT INTO Colis (id_bureau_depart, id_citoyen, numero_suivi, statut)
             VALUES (:id_bureau, :id_citoyen, :numero, :statut)'
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
                    ]);
                    $enregistre = true;
                } catch (PDOException $erreur) {
                    // Un numéro déjà attribué est remplacé par un nouveau numéro.
                    if (!isset($erreur->errorInfo[1]) || $erreur->errorInfo[1] !== 1062) {
                        throw $erreur;
                    }
                }
            }

            $colis = $this->findParcel((int) $this->connexion->lastInsertId());
            $this->connexion->commit();
            return $colis;
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

    public function updateParcelStatus($id, $statut, $statutActuel)
    {
        if (!in_array($statut, self::STATUSES, true)) {
            throw new InvalidArgumentException('Statut de colis invalide.');
        }
        if (!in_array($statut, self::statutsSuivants($statutActuel), true)) {
            return false;
        }
        // Une déclaration annulée ou déjà traitée ne peut pas être écrasée.
        $requete = $this->connexion->prepare(
            'UPDATE Colis SET statut = :statut WHERE id_colis = :id AND statut = :statut_actuel'
        );
        $requete->execute(['statut' => $statut, 'id' => $id, 'statut_actuel' => $statutActuel]);
        return $requete->rowCount() === 1;
    }

    public function cancelParcel($id, $idCitoyen)
    {
        $requete = $this->connexion->prepare(
            'UPDATE Colis SET statut = :statut_annule
             WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut_initial'
        );
        $requete->execute([
            'statut_annule' => 'Annulé',
            'id' => $id,
            'id_citoyen' => $idCitoyen,
            'statut_initial' => 'En attente',
        ]);
        return $requete->rowCount() === 1;
    }

    public function deleteParcel($id)
    {
        $requete = $this->connexion->prepare('DELETE FROM Colis WHERE id_colis = :id');
        $requete->execute(['id' => $id]);
        return $requete->rowCount() === 1;
    }

    public function chercherColis($numeroSuivi)
    {
        return $this->track($numeroSuivi);
    }

    public function listerBureaux()
    {
        return $this->listOffices();
    }

    public function filtrerBureaux($ville)
    {
        $bureaux = $this->listerBureaux();
        $villes = [];
        foreach ($bureaux as $bureau) {
            if (!in_array($bureau['ville'], $villes, true)) {
                $villes[] = $bureau['ville'];
            }
        }
        natcasesort($villes);

        if (!is_string($ville) || !in_array($ville, $villes, true)) {
            $ville = '';
        }

        $resultats = [];
        foreach ($bureaux as $bureau) {
            if ($ville === '' || $bureau['ville'] === $ville) {
                $resultats[] = $bureau;
            }
        }

        return ['bureaux' => $resultats, 'villes' => $villes, 'ville' => $ville];
    }

    public function declarerEnvoi($idRecu)
    {
        exigerRole(['citoyen']);
        $utilisateur = utilisateurConnecte();
        $idBureau = $this->identifiantValide($idRecu);
        if ($idBureau === null || !$this->findOffice($idBureau)) {
            http_response_code(422);
            return tr('Choisissez un bureau de départ dans la liste.', 'اختر مكتب الإرسال من القائمة.');
        }

        try {
            $colis = $this->createParcel($idBureau, $utilisateur['id']);
        } catch (PDOException $erreur) {
            if ($this->findOffice($idBureau) !== null) {
                throw $erreur;
            }

            http_response_code(409);
            return tr('Ce bureau n’est plus disponible. Choisissez un autre bureau de départ.', 'لم يعد هذا المكتب متاحًا. اختر مكتب إرسال آخر.');
        }

        $this->retourHistorique(['kind' => 'declared', 'number' => $colis['numero_suivi']]);
    }

    public function listerHistorique()
    {
        exigerRole(['citoyen']);
        $utilisateur = utilisateurConnecte();
        return $this->listParcels($utilisateur['id']);
    }

    public function annulerEnvoi($idRecu)
    {
        exigerRole(['citoyen']);
        $utilisateur = utilisateurConnecte();
        $idColis = $this->identifiantValide($idRecu);
        if ($idColis !== null && $this->cancelParcel($idColis, $utilisateur['id'])) {
            $this->retourHistorique(['kind' => 'cancelled']);
        }

        http_response_code(409);
        return tr('Ce colis ne peut plus être annulé. Seuls les envois en attente sont annulables.', 'لا يمكن إلغاء هذا الطرد. يمكن إلغاء الإرساليات قيد الانتظار فقط.');
    }

    private function identifiantValide($valeur)
    {
        if (!is_string($valeur)) {
            return null;
        }

        $id = filter_var($valeur, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return null;
        }
        return $id;
    }

    private function retourHistorique($confirmation)
    {
        // La redirection évite de renvoyer le formulaire après un rafraîchissement.
        $_SESSION['poste_front_flash'] = $confirmation;
        header('Location: ' . frontUrl('historique'), true, 303);
        exit;
    }
}
