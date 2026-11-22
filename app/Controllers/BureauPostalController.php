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
        CitoyenController::requireAdmin();
        $id = posteId($_GET) ?? posteId($_POST);
        $old = $id === null ? null : $this->recupererBureau($id);
        if (!$old) {
            $this->introuvable();
            return;
        }
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id_bureau'] = $id;
            $erreurs = $this->verifierBureau($old);
            if (count($erreurs) == 0) {
                $bureau = new BureauPostal($id, $old['nom'], $old['adresse'], $old['ville']);
                if (!$this->modifierBureau($bureau, $id)) {
                    $this->introuvable();
                    return;
                }
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le bureau a été mis à jour.'];
                header('Location: index.php?page=admin-bureaux', true, 303);
                exit;
            }
            http_response_code(422);
        }
        $this->renderBack('poste/updateBureau', [
            'title' => 'Modifier le bureau', 'currentPage' => 'admin-bureaux', 'section' => 'bureaux',
            'old' => $old, 'erreurs' => $erreurs,
        ]);
    }

    public function deleteBack(): void {
        CitoyenController::requireAdmin();
        $id = $_SERVER['REQUEST_METHOD'] === 'POST' ? posteId($_POST) : posteId($_GET);
        $bureau = $id === null ? null : $this->recupererBureau($id);
        if (!$bureau) {
            $this->introuvable();
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supprime = $this->supprimerBureau($id);
            $_SESSION['flash'] = [
                'type' => $supprime ? 'success' : 'warning',
                'message' => $supprime ? 'Le bureau a été supprimé.' : 'Ce bureau est associé à des colis et ne peut pas être supprimé.',
            ];
            header('Location: index.php?page=admin-bureaux', true, 303);
            exit;
        }
        $this->renderBack('commun/supprimer', [
            'title' => 'Supprimer le bureau', 'currentPage' => 'admin-bureaux', 'section' => 'bureaux', 'erreurs' => [],
            'old' => ['id' => $id, 'reference' => $bureau['nom'], 'action' => 'deleteBureau',
                'message' => 'La suppression est possible si aucun colis n’est associé à ce bureau.'],
        ]);
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

    private function introuvable(): void {
        http_response_code(404);
        $this->renderBack('commun/problem', [
            'title' => 'Bureau introuvable', 'currentPage' => 'admin-bureaux', 'section' => 'bureaux',
            'message' => 'Ce bureau n’existe plus ou le lien est incorrect.',
        ]);
    }
}
