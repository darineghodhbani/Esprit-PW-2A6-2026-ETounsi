<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Propriete.php';

class ProprieteController extends Controller {

    public const TYPES = [
        'maison' => 'Maison', 'appartement' => 'Appartement', 'terrain' => 'Terrain',
        'voiture' => 'Voiture', 'local_commercial' => 'Local commercial',
    ];

    public function listerProprietes() {
        $sql = "SELECT propriete.*, CONCAT(citoyen.prenom, ' ', citoyen.nom) AS proprietaire
                FROM propriete JOIN citoyen ON propriete.id_citoyen = citoyen.id_citoyen
                ORDER BY propriete.id ASC";
        $db = Database::getConnection();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function listerProprietesCitoyen($idCitoyen) {
        $sql = "SELECT * FROM propriete WHERE id_citoyen = :id_citoyen ORDER BY id ASC";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_citoyen' => $idCitoyen]);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function recupererPropriete($id) {
        $sql = "SELECT * FROM propriete WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function compterProprietes() {
        $sql = "SELECT COUNT(*) FROM propriete";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetchColumn();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function listerCitoyens() {
        $sql = "SELECT id_citoyen, nom, prenom FROM citoyen WHERE role = 'citoyen' ORDER BY nom ASC";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function ajouterPropriete(Propriete $propriete) {
        $sql = "INSERT INTO propriete (id_citoyen, type, reference, adresse, valeur)
                VALUES (:id_citoyen, :type, :reference, :adresse, :valeur)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_citoyen' => $propriete->getIdCitoyen(),
                'type' => $propriete->getType(),
                'reference' => $propriete->getReference(),
                'adresse' => $propriete->getAdresse(),
                'valeur' => $propriete->getValeur()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function modifierPropriete(Propriete $propriete, $id) {
        $sql = "UPDATE propriete SET id_citoyen = :id_citoyen, type = :type, reference = :reference,
                adresse = :adresse, valeur = :valeur WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_citoyen' => $propriete->getIdCitoyen(),
                'type' => $propriete->getType(),
                'reference' => $propriete->getReference(),
                'adresse' => $propriete->getAdresse(),
                'valeur' => $propriete->getValeur(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function supprimerPropriete($id) {
        $sql = "DELETE FROM propriete WHERE id = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public static function clean(array $post): array {
        return [
            'type'      => trim($post['type']),
            'reference' => trim($post['reference']),
            'adresse'   => trim($post['adresse']),
            'valeur'    => trim($post['valeur']),
        ];
    }

    public function verifierPropriete(array $d): array {
        $erreurs = [];

        if ($d['id_citoyen'] <= 0) {
            $erreurs['id_citoyen'] = 'Veuillez choisir un citoyen.';
        }

        if (!isset(self::TYPES[$d['type']])) {
            $erreurs['type'] = 'Veuillez choisir un type.';
        }

        if ($d['reference'] === '') {
            $erreurs['reference'] = 'La référence est obligatoire.';
        } elseif (strlen($d['reference']) < 3 || strlen($d['reference']) > 50) {
            $erreurs['reference'] = 'La référence doit contenir 3 à 50 caractères.';
        }

        if ($d['adresse'] === '') {
            $erreurs['adresse'] = "L'adresse est obligatoire.";
        } elseif (strlen($d['adresse']) < 5 || strlen($d['adresse']) > 150) {
            $erreurs['adresse'] = "L'adresse doit contenir 5 à 150 caractères.";
        }

        if ($d['valeur'] === '') {
            $erreurs['valeur'] = 'La valeur est obligatoire.';
        } elseif (!ctype_digit($d['valeur']) || $d['valeur'] == 0) {
            $erreurs['valeur'] = 'La valeur doit être un nombre entier positif.';
        }

        return $erreurs;
    }

    public function declarer(): void {
        CitoyenController::requireLogin();
        $idCitoyen = (int) $_SESSION['id_citoyen'];

        $erreurs = [];
        $old = ['type' => '', 'reference' => '', 'adresse' => '', 'valeur' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id_citoyen'] = $idCitoyen;
            $erreurs = $this->verifierPropriete($old);

            if (count($erreurs) == 0) {
                $propriete = new Propriete(null, $idCitoyen, $old['type'], $old['reference'], $old['adresse'], (int) $old['valeur']);
                $this->ajouterPropriete($propriete);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Propriété ajoutée avec succès.'];
                header('Location: index.php?page=declarer-propriete');
                exit;
            }
        }

        $this->renderFront('finances/declarerPropriete', [
            'title'         => 'Déclarer une propriété — E-Tounsi',
            'currentPage'   => 'declarer-propriete',
            'erreurs'       => $erreurs,
            'old'           => $old,
            'mesProprietes' => $this->listerProprietesCitoyen($idCitoyen),
        ]);
    }

    public function listBack(): void {
        CitoyenController::requireAdmin();

        $this->renderBack('finances/proprietes', [
            'title'       => 'Propriétés — E-Tounsi',
            'currentPage' => 'admin-proprietes',
            'liste'       => $this->listerProprietes(),
            'total'       => $this->compterProprietes(),
        ]);
    }

    public function addBack(): void {
        CitoyenController::requireAdmin();

        $erreurs = [];
        $old = ['id_citoyen' => 0, 'type' => '', 'reference' => '', 'adresse' => '', 'valeur' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id_citoyen'] = (int) $_POST['id_citoyen'];
            $erreurs = $this->verifierPropriete($old);

            if (count($erreurs) == 0) {
                $propriete = new Propriete(null, $old['id_citoyen'], $old['type'], $old['reference'], $old['adresse'], (int) $old['valeur']);
                $this->ajouterPropriete($propriete);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Propriété ajoutée avec succès.'];
                header('Location: index.php?page=admin-proprietes');
                exit;
            }
        }

        $this->renderBack('finances/addPropriete', [
            'title'       => 'Ajouter une propriété — E-Tounsi',
            'currentPage' => 'admin-proprietes',
            'erreurs'     => $erreurs,
            'old'         => $old,
            'citoyens'    => $this->listerCitoyens(),
        ]);
    }

    public function updateBack(): void {
        CitoyenController::requireAdmin();

        $id = (int) $_GET['id'];
        $erreurs = [];
        $old = $this->recupererPropriete($id);

        if (!$old) {
            header('Location: index.php?page=admin-proprietes');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id_citoyen'] = (int) $_POST['id_citoyen'];
            $erreurs = $this->verifierPropriete($old);

            if (count($erreurs) == 0) {
                $propriete = new Propriete($id, $old['id_citoyen'], $old['type'], $old['reference'], $old['adresse'], (int) $old['valeur']);
                $this->modifierPropriete($propriete, $id);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Propriété modifiée avec succès.'];
                header('Location: index.php?page=admin-proprietes');
                exit;
            }
        }

        $this->renderBack('finances/updatePropriete', [
            'title'       => 'Modifier une propriété — E-Tounsi',
            'currentPage' => 'admin-proprietes',
            'id'          => $id,
            'erreurs'     => $erreurs,
            'old'         => $old,
            'citoyens'    => $this->listerCitoyens(),
        ]);
    }

    public function deleteBack(): void {
        CitoyenController::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->supprimerPropriete((int) $_POST['id']);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Propriété supprimée.'];
        }
        header('Location: index.php?page=admin-proprietes');
        exit;
    }
}
