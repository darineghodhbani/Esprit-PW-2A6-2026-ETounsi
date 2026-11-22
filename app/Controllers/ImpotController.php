<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/ProprieteController.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Impot.php';

class ImpotController extends Controller {
    private $modele;

    public function __construct() {
        $this->modele = new Impot();
    }


    public const STATUTS = ['non_paye' => 'Non payé', 'en_attente' => 'En attente de validation', 'paye' => 'Payé'];

    public function listerImpots()
    {
        return $this->modele->listerImpots();
    }

    public function listerImpotsCitoyen($idCitoyen)
    {
        return $this->modele->listerImpotsCitoyen($idCitoyen);
    }

    public function listerImpotsPayes($idCitoyen)
    {
        return $this->modele->listerImpotsPayes($idCitoyen);
    }

    public function recupererImpot($id)
    {
        return $this->modele->recupererImpot($id);
    }

    public function compterImpots($statut)
    {
        return $this->modele->compterImpots($statut);
    }

    public function ajouterImpot(Impot $impot)
    {
        return $this->modele->ajouterImpot($impot);
    }

    public function modifierImpot(Impot $impot, $id)
    {
        return $this->modele->modifierImpot($impot, $id);
    }

    public function supprimerImpot($id)
    {
        return $this->modele->supprimerImpot($id);
    }

    public function recupererImpotCitoyen($id, $idCitoyen)
    {
        return $this->modele->recupererImpotCitoyen($id, $idCitoyen);
    }

    public function changerStatut($id, $statut)
    {
        return $this->modele->changerStatut($id, $statut);
    }

    public static function clean(array $post): array {
        return [
            'id_propriete' => (int) $post['id_propriete'],
            'annee'        => trim($post['annee']),
            'montant'      => trim($post['montant']),
            'date_limite'  => trim($post['date_limite']),
            'statut'       => trim($post['statut']),
        ];
    }

    public function verifierImpot(array $d): array {
        $erreurs = [];

        if ($d['id_propriete'] <= 0) {
            $erreurs['id_propriete'] = 'Veuillez choisir une propriété.';
        }

        if ($d['annee'] === '') {
            $erreurs['annee'] = "L'année est obligatoire.";
        } elseif (!ctype_digit($d['annee']) || $d['annee'] < 2000 || $d['annee'] > 2100) {
            $erreurs['annee'] = "L'année doit être comprise entre 2000 et 2100.";
        }

        if ($d['montant'] === '') {
            $erreurs['montant'] = 'Le montant est obligatoire.';
        } elseif (!ctype_digit($d['montant']) || $d['montant'] == 0) {
            $erreurs['montant'] = 'Le montant doit être un nombre entier positif.';
        }

        if ($d['date_limite'] === '') {
            $erreurs['date_limite'] = 'La date limite est obligatoire.';
        }

        if (!isset(self::STATUTS[$d['statut']])) {
            $erreurs['statut'] = 'Veuillez choisir un statut.';
        }

        return $erreurs;
    }

    public function payer(): void {
        CitoyenController::requireLogin();
        $idCitoyen = (int) $_SESSION['id_citoyen'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $impot = $this->recupererImpotCitoyen((int) $_POST['id'], $idCitoyen);

            if ($impot && $impot['statut'] == 'non_paye') {
                $this->changerStatut($impot['id'], 'en_attente');
                $_SESSION['flash'] = ['type' => 'success', 'message' =>
                    "Demande enregistrée. Rendez-vous au bureau de poste pour régler le montant. Votre impôt sera marqué « Payé » après validation par l'administration."];
            } else {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Paiement impossible.'];
            }
            header('Location: index.php?page=payer-impot');
            exit;
        }

        $this->renderFront('finances/payerImpot', [
            'title'       => 'Payer mes impôts — E-Tounsi',
            'currentPage' => 'payer-impot',
            'mesImpots'   => $this->listerImpotsCitoyen($idCitoyen),
        ]);
    }

    public function recus(): void {
        CitoyenController::requireLogin();

        $this->renderFront('finances/mesRecus', [
            'title'       => 'Mes reçus — E-Tounsi',
            'currentPage' => 'mes-recus',
            'recus'       => $this->listerImpotsPayes((int) $_SESSION['id_citoyen']),
        ]);
    }

    public function listBack(): void {
        CitoyenController::requireAdmin();

        $this->renderBack('finances/impots', [
            'title'       => 'Impôts — E-Tounsi',
            'currentPage' => 'admin-impots',
            'liste'       => $this->listerImpots(),
            'nbNonPayes'  => $this->compterImpots('non_paye'),
            'nbEnAttente' => $this->compterImpots('en_attente'),
            'nbPayes'     => $this->compterImpots('paye'),
        ]);
    }

    public function addBack(): void {
        CitoyenController::requireAdmin();

        $erreurs = [];
        $old = ['id_propriete' => 0, 'annee' => '', 'montant' => '', 'date_limite' => '', 'statut' => 'non_paye'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $erreurs = $this->verifierImpot($old);

            if (count($erreurs) == 0) {
                $impot = new Impot(null, $old['id_propriete'], (int) $old['annee'], (int) $old['montant'], $old['date_limite'], $old['statut']);
                $this->ajouterImpot($impot);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Impôt ajouté avec succès.'];
                header('Location: index.php?page=admin-impots');
                exit;
            }
        }

        $proprieteController = new ProprieteController();
        $this->renderBack('finances/addImpot', [
            'title'       => 'Ajouter un impôt — E-Tounsi',
            'currentPage' => 'admin-impots',
            'erreurs'     => $erreurs,
            'old'         => $old,
            'proprietes'  => $proprieteController->listerProprietes(),
        ]);
    }

    public function updateBack(): void {
        CitoyenController::requireAdmin();

        $id = (int) $_GET['id'];
        $erreurs = [];
        $old = $this->recupererImpot($id);

        if (!$old) {
            header('Location: index.php?page=admin-impots');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $erreurs = $this->verifierImpot($old);

            if (count($erreurs) == 0) {
                $impot = new Impot($id, $old['id_propriete'], (int) $old['annee'], (int) $old['montant'], $old['date_limite'], $old['statut']);
                $this->modifierImpot($impot, $id);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Impôt modifié avec succès.'];
                header('Location: index.php?page=admin-impots');
                exit;
            }
        }

        $proprieteController = new ProprieteController();
        $this->renderBack('finances/updateImpot', [
            'title'       => 'Modifier un impôt — E-Tounsi',
            'currentPage' => 'admin-impots',
            'id'          => $id,
            'erreurs'     => $erreurs,
            'old'         => $old,
            'proprietes'  => $proprieteController->listerProprietes(),
        ]);
    }

    public function deleteBack(): void {
        CitoyenController::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->supprimerImpot((int) $_POST['id']);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Impôt supprimé.'];
        }
        header('Location: index.php?page=admin-impots');
        exit;
    }
}
