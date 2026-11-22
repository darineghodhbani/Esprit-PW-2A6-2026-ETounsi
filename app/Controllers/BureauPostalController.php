<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/BureauPostal.php';
require_once __DIR__ . '/../poste.php';

class BureauPostalController extends Controller
{
    private $connexion;
    private $modele;

    public function __construct($connexion = null)
    {
        $this->connexion = $connexion ?? Database::getConnection();
        $this->modele = new BureauPostal();
    }

    public function listeBureaux()
    {
        return $this->modele->listeBureaux();
    }

    public function recupererBureau($id)
    {
        return $this->modele->recupererBureau($id);
    }

    public function ajouterBureau(BureauPostal $bureau)
    {
        return $this->modele->ajouterBureau($bureau);
    }

    public function modifierBureau(BureauPostal $bureau)
    {
        return $this->modele->modifierBureau($bureau);
    }

    public function deleteBureau($id)
    {
        return $this->modele->deleteBureau($id);
    }

    public function filtrerBureaux($ville)
    {
        $bureaux = $this->listeBureaux();
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


    private const FIELDS = [
        'nom' => ['label' => 'Nom du bureau', 'limit' => 100],
        'adresse' => ['label' => 'Adresse', 'limit' => 200],
        'ville' => ['label' => 'Ville', 'limit' => 80],
    ];

    public function index()
    {
        $query = posteText($_GET, 'q');
        $offices = [];
        foreach ($this->listeBureaux() as $office) {
            $coordinates = $office['nom'] . ' ' . $office['adresse'] . ' ' . $office['ville'];
            if (posteMatches($query, $coordinates)) {
                $offices[] = $office;
            }
        }

        posteRender('bureauList', 'Bureaux de poste', 'bureaux', ['offices' => $offices, 'query' => $query]);
    }

    public function form()
    {
        $office = ['nom' => '', 'adresse' => '', 'ville' => ''];
        if (isset($_GET['id'])) {
            $office = $this->find(posteId($_GET));
        }
        if ($office !== null) {
            $this->renderForm($office);
        }
    }

    public function save()
    {
        if (!posteCsrfValid()) {
            return;
        }

        $id = null;
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $id = posteId($_POST);
            if ($this->find($id) === null) {
                return;
            }
        }

        $office = ['id_bureau' => $id];
        $errors = [];
        foreach (self::FIELDS as $field => $info) {
            $value = posteText($_POST, $field);
            $office[$field] = $value;
            $limit = $info['limit'];
            if ($value === '') {
                $errors[$field] = 'Renseignez ce champ.';
            } elseif (mb_strlen($value, 'UTF-8') > $limit) {
                $errors[$field] = "Utilisez au maximum $limit caractères.";
            }
        }

        if ($errors !== []) {
            $this->renderForm($office, $errors);
            return;
        }

        try {
            $bureau = new BureauPostal($id, $office['nom'], $office['adresse'], $office['ville']);
            if ($id === null) {
                $this->ajouterBureau($bureau);
            } else {
                $this->modifierBureau($bureau);
            }
        } catch (OutOfBoundsException $error) {
            posteProblem(404, 'Bureau introuvable', 'Ce bureau a été supprimé pendant la modification.', 'bureaux');
            return;
        }

        if ($id === null) {
            posteRedirect('bureaux', 'Le bureau a été ajouté.');
        }
        posteRedirect('bureaux', 'Le bureau a été mis à jour.');
    }

    public function confirmDelete()
    {
        $office = $this->find(posteId($_GET));
        if ($office !== null) {
            posteRender('supprimer', 'Supprimer le bureau', 'bureaux', [
                'id' => $office['id_bureau'],
                'reference' => $office['nom'],
                'action' => 'deleteBureau',
                'message' => 'Le bureau sera retiré de l’annuaire. La suppression est possible si aucun colis n’est associé à ce bureau.',
            ]);
        }
    }

    public function delete()
    {
        if (!posteCsrfValid()) {
            return;
        }

        $id = posteId($_POST);
        if ($this->find($id) === null) {
            return;
        }

        if (!$this->deleteBureau($id)) {
            posteRedirect('bureaux', 'Ce bureau est associé à des colis ou a déjà été supprimé.', 'warning');
        }
        posteRedirect('bureaux', 'Le bureau a été supprimé.');
    }

    private function find($id)
    {
        $office = null;
        if ($id !== null) {
            $office = $this->recupererBureau($id);
        }
        if ($office === null) {
            posteProblem(404, 'Bureau introuvable', 'Ce bureau n’existe plus ou le lien est incorrect.', 'bureaux');
        }
        return $office;
    }

    private function renderForm($office, $errors = [])
    {
        if ($errors !== []) {
            http_response_code(422);
        }

        $title = 'Ajouter un bureau';
        if (!empty($office['id_bureau'])) {
            $title = 'Modifier le bureau';
        }
        posteRender('bureauForm', $title, 'bureaux', [
            'office' => $office,
            'fields' => self::FIELDS,
            'errors' => $errors,
        ]);
    }

    public function listFront(): void {
        posteFront('bureaux', tr('Bureaux de poste', 'مكاتب البريد'), ['annuaire' => $this->filtrerBureaux($_GET['ville'] ?? '')]);
    }
}
