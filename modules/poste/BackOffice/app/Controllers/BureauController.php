<?php

class BureauController extends Controller
{
    private const FIELDS = [
        'nom' => ['label' => 'Nom du bureau', 'limit' => 100],
        'adresse' => ['label' => 'Adresse', 'limit' => 200],
        'ville' => ['label' => 'Ville', 'limit' => 80],
    ];

    public function index()
    {
        $query = $this->text($_GET, 'q');
        $offices = [];
        foreach (posteRepository()->listOffices() as $office) {
            $coordinates = $office['nom'] . ' ' . $office['adresse'] . ' ' . $office['ville'];
            if ($this->matches($query, $coordinates)) {
                $offices[] = $office;
            }
        }

        $this->render('bureaux', 'Bureaux de poste', 'bureaux', ['offices' => $offices, 'query' => $query]);
    }

    public function form()
    {
        $office = ['nom' => '', 'adresse' => '', 'ville' => ''];
        if (isset($_GET['id'])) {
            $office = $this->find($this->id($_GET));
        }
        if ($office !== null) {
            $this->renderForm($office);
        }
    }

    public function save()
    {
        if (!$this->csrfValid()) {
            return;
        }

        $id = null;
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $id = $this->id($_POST);
            if ($this->find($id) === null) {
                return;
            }
        }

        $office = ['id_bureau' => $id];
        $errors = [];
        foreach (self::FIELDS as $field => $info) {
            $value = $this->text($_POST, $field);
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
            posteRepository()->saveOffice($id, $office['nom'], $office['adresse'], $office['ville']);
        } catch (OutOfBoundsException $error) {
            $this->problem(404, 'Bureau introuvable', 'Ce bureau a été supprimé pendant la modification.', 'bureaux');
            return;
        }

        if ($id === null) {
            $this->redirect('bureaux', 'Le bureau a été ajouté.');
        }
        $this->redirect('bureaux', 'Le bureau a été mis à jour.');
    }

    public function confirmDelete()
    {
        $office = $this->find($this->id($_GET));
        if ($office !== null) {
            $this->render('suppression', 'Supprimer le bureau', 'bureaux', [
                'id' => $office['id_bureau'],
                'reference' => $office['nom'],
                'action' => 'bureau-delete',
                'message' => 'Le bureau sera retiré de l’annuaire. La suppression est possible si aucun colis n’est associé à ce bureau.',
            ]);
        }
    }

    public function delete()
    {
        if (!$this->csrfValid()) {
            return;
        }

        $id = $this->id($_POST);
        if ($this->find($id) === null) {
            return;
        }

        if (!posteRepository()->deleteOffice($id)) {
            $this->redirect('bureaux', 'Ce bureau est associé à des colis ou a déjà été supprimé.', 'warning');
        }
        $this->redirect('bureaux', 'Le bureau a été supprimé.');
    }

    private function find($id)
    {
        $office = null;
        if ($id !== null) {
            $office = posteRepository()->findOffice($id);
        }
        if ($office === null) {
            $this->problem(404, 'Bureau introuvable', 'Ce bureau n’existe plus ou le lien est incorrect.', 'bureaux');
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
        $this->render('bureau-form', $title, 'bureaux', [
            'office' => $office,
            'fields' => self::FIELDS,
            'errors' => $errors,
        ]);
    }
}
