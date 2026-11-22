<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/BureauPostal.php';

class BureauPostalController extends Controller {
    private $modele;

    public function __construct() {
        $this->modele = new BureauPostal();
    }

    public function listerBureaux() {
        return $this->modele->listerBureaux();
    }

    public function recupererBureau($id) {
        return $this->modele->recupererBureau($id);
    }

    public function compterBureaux() {
        return $this->modele->compterBureaux();
    }

    public function compterVilles() {
        return $this->modele->compterVilles();
    }

    public function ajouterBureau(BureauPostal $bureau) {
        return $this->modele->ajouterBureau($bureau);
    }

    public function modifierBureau(BureauPostal $bureau, $id) {
        return $this->modele->modifierBureau($bureau, $id);
    }

    public function supprimerBureau($id) {
        return $this->modele->supprimerBureau($id);
    }

    public static function clean(array $post): array {
        return [
            'nom' => posteText($post, 'nom'),
            'adresse' => posteText($post, 'adresse'),
            'ville' => posteText($post, 'ville'),
        ];
    }

    public function verifierBureau(array $d): array {
        $erreurs = [];
        if ($d['nom'] === '') {
            $erreurs['nom'] = 'Le nom du bureau est obligatoire.';
        } elseif (mb_strlen($d['nom']) > 100) {
            $erreurs['nom'] = 'Le nom doit contenir au maximum 100 caractères.';
        }
        if ($d['adresse'] === '') {
            $erreurs['adresse'] = 'L’adresse est obligatoire.';
        } elseif (mb_strlen($d['adresse']) > 200) {
            $erreurs['adresse'] = 'L’adresse doit contenir au maximum 200 caractères.';
        }
        if ($d['ville'] === '') {
            $erreurs['ville'] = 'La ville est obligatoire.';
        } elseif (mb_strlen($d['ville']) > 80) {
            $erreurs['ville'] = 'La ville doit contenir au maximum 80 caractères.';
        }
        return $erreurs;
    }

    public function listBack(): void {
        $this->renderBack('poste/bureauList');
    }

    public function addBack(): void {
        $this->renderBack('poste/addBureau');
    }

    public function updateBack(): void {
        $this->renderBack('poste/updateBureau');
    }

    public function deleteBack(): void {
        require __DIR__ . '/../Views/backoffice/pages/poste/deleteBureau.php';
    }

    public function listFront(): void {
        $bureaux = $this->listerBureaux();
        $villes = array_values(array_unique(array_column($bureaux, 'ville')));
        natcasesort($villes);
        $ville = posteText($_GET, 'ville');
        if (!in_array($ville, $villes, true)) $ville = '';
        $liste = [];
        foreach ($bureaux as $bureau) {
            if ($ville === '' || $bureau['ville'] === $ville) $liste[] = $bureau;
        }
        $this->renderFront('poste/bureaux', [
            'title' => t('poste.bureaux'), 'currentPage' => 'bureaux-poste',
            'annuaire' => ['bureaux' => $liste, 'villes' => $villes, 'ville' => $ville],
        ]);
    }

}
