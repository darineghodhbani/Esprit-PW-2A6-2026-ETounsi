<?php

class ColisController extends Controller
{
    public function index()
    {
        $query = $this->text($_GET, 'q');
        $statuses = PosteController::STATUSES;
        $statusFilter = $this->text($_GET, 'statut');
        if (!in_array($statusFilter, $statuses, true)) {
            $statusFilter = '';
        }

        $parcels = [];
        foreach (gestionPoste()->listParcels() as $parcel) {
            $matchesStatus = $statusFilter === '' || $parcel['statut'] === $statusFilter;
            $parcelInfo = $parcel['numero_suivi'] . ' ' . $parcel['bureau'] . ' ' . $parcel['ville'];
            if ($matchesStatus && $this->matches($query, $parcelInfo)) {
                $parcels[] = $parcel;
            }
        }

        $this->render('colis', 'Gestion des colis', 'colis', [
            'parcels' => $parcels,
            'query' => $query,
            'statusFilter' => $statusFilter,
            'statuses' => $statuses,
        ]);
    }

    public function form()
    {
        $parcel = ['id_citoyen' => 1, 'id_bureau_depart' => ''];
        if (isset($_GET['id'])) {
            $parcel = $this->find($this->id($_GET));
        }
        if ($parcel !== null) {
            $this->renderForm($parcel);
        }
    }

    public function save()
    {
        if (!$this->csrfValid()) {
            return;
        }

        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $this->saveStatus();
            return;
        }

        $officeId = $this->id($_POST, 'id_bureau_depart');
        $citizenId = $this->id($_POST, 'id_citoyen');
        $parcel = [
            'id_bureau_depart' => $this->text($_POST, 'id_bureau_depart'),
            'id_citoyen' => $this->text($_POST, 'id_citoyen'),
        ];
        $repository = gestionPoste();
        $errors = [];
        if ($officeId === null || $repository->findOffice($officeId) === null) {
            $errors['id_bureau_depart'] = 'Choisissez un bureau de départ disponible.';
        }

        $citizenFound = false;
        foreach ($repository->listCitizens() as $citizen) {
            if ((int) $citizen['id_citoyen'] === $citizenId) {
                $citizenFound = true;
                break;
            }
        }
        if (!$citizenFound) {
            $errors['id_citoyen'] = 'Choisissez un compte citoyen disponible.';
        }
        if ($errors !== []) {
            $this->renderForm($parcel, $errors);
            return;
        }

        try {
            $parcel = $repository->createParcel($officeId, $citizenId);
        } catch (PDOException $error) {
            // Le bureau peut avoir été supprimé après l’ouverture du formulaire.
            if ($error->getCode() !== '23000') {
                throw $error;
            }
            $this->renderForm($parcel, ['id_bureau_depart' => 'Vérifiez le bureau de départ et le compte citoyen.']);
            return;
        }
        $this->redirect('colis', 'Le colis ' . $parcel['numero_suivi'] . ' a été enregistré.');
    }

    public function confirmDelete()
    {
        $parcel = $this->find($this->id($_GET));
        if ($parcel !== null) {
            $this->render('suppression', 'Supprimer le colis', 'colis', [
                'id' => $parcel['id_colis'],
                'reference' => $parcel['numero_suivi'],
                'action' => 'colis-delete',
                'message' => 'Le colis sera retiré du suivi et de l’historique citoyen. Cette action est définitive.',
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
        if (!gestionPoste()->deleteParcel($id)) {
            $this->redirect('colis', 'Ce colis a déjà été supprimé.', 'warning');
        }
        $this->redirect('colis', 'Le colis a été supprimé.');
    }

    private function saveStatus()
    {
        $id = $this->id($_POST);
        $parcel = $this->find($id);
        if ($parcel === null) {
            return;
        }

        $status = $this->text($_POST, 'statut');
        if (!in_array($status, PosteController::STATUSES, true)) {
            $this->renderForm($parcel, ['statut' => 'Choisissez un statut dans la liste.']);
            return;
        }
        if (!gestionPoste()->updateParcelStatus($id, $status)) {
            $this->redirect('colis', 'Le changement de statut n’a pas été enregistré. Vérifiez le colis.', 'warning');
        }

        $this->redirect('colis', 'Le statut du colis a été mis à jour.');
    }

    private function find($id)
    {
        $parcel = null;
        if ($id !== null) {
            $parcel = gestionPoste()->findParcel($id);
        }
        if ($parcel === null) {
            $this->problem(404, 'Colis introuvable', 'Ce colis n’existe plus ou le lien est incorrect.', 'colis');
        }
        return $parcel;
    }

    private function renderForm($parcel, $errors = [])
    {
        if ($errors !== []) {
            http_response_code(422);
        }

        $editing = !empty($parcel['id_colis']);
        $title = 'Modifier le statut';
        $offices = [];
        $citizens = [];
        if (!$editing) {
            $title = 'Enregistrer un colis';
            $repository = gestionPoste();
            $offices = $repository->listOffices();
            $citizens = $repository->listCitizens();
        }
        $this->render('colis-form', $title, 'colis', [
            'parcel' => $parcel,
            'editing' => $editing,
            'statuses' => PosteController::STATUSES,
            'offices' => $offices,
            'citizens' => $citizens,
            'errors' => $errors,
        ]);
    }
}
