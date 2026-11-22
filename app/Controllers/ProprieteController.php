<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Propriete.php';

class ProprieteController extends Controller {
    private $modele;

    public function __construct() {
        $this->modele = new Propriete();
    }


    public const TYPES = [
        'maison' => 'Maison', 'appartement' => 'Appartement', 'terrain' => 'Terrain',
        'voiture' => 'Voiture', 'local_commercial' => 'Local commercial',
    ];

    public function listerProprietes()
    {
        return $this->modele->listerProprietes();
    }

    public function listerProprietesCitoyen($idCitoyen)
    {
        return $this->modele->listerProprietesCitoyen($idCitoyen);
    }

    public function recupererPropriete($id)
    {
        return $this->modele->recupererPropriete($id);
    }

    public function compterProprietes()
    {
        return $this->modele->compterProprietes();
    }

    public function listerCitoyens()
    {
        return $this->modele->listerCitoyens();
    }

    public function ajouterPropriete(Propriete $propriete)
    {
        return $this->modele->ajouterPropriete($propriete);
    }

    public function modifierPropriete(Propriete $propriete, $id)
    {
        return $this->modele->modifierPropriete($propriete, $id);
    }

    public function supprimerPropriete($id)
    {
        return $this->modele->supprimerPropriete($id);
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
