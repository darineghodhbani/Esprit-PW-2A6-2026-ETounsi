<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Colis.php';
require_once __DIR__ . '/../poste.php';
require_once __DIR__ . '/BureauPostalController.php';

class ColisController extends Controller
{
    public const STATUTS = ['En attente', 'Accepté', 'En transit', 'Livré', 'Annulé'];

    private $connexion;
    private $modele;

    public function __construct($connexion = null)
    {
        $this->connexion = $connexion ?? Database::getConnection();
        $this->modele = new Colis();
    }

    public function listeCitoyens()
    {
        return $this->modele->listeCitoyens();
    }

    public function listeColis($idCitoyen = null)
    {
        return $this->modele->listeColis($idCitoyen);
    }

    public function recupererColis($id)
    {
        return $this->modele->recupererColis($id);
    }

    public function chercherColis($numero)
    {
        return $this->modele->chercherColis($numero);
    }

    public function ajouterColis($idBureau, $idCitoyen)
    {
        return $this->modele->ajouterColis($idBureau, $idCitoyen);
    }

    public static function statutsSuivants($statut)
    {
        return Colis::statutsSuivants($statut);
    }

    public function modifierColis($id, $statut, $statutActuel)
    {
        return $this->modele->modifierColis($id, $statut, $statutActuel);
    }

    public function annulerColis($id, $idCitoyen)
    {
        return $this->modele->annulerColis($id, $idCitoyen);
    }

    public function deleteColis($id)
    {
        return $this->modele->deleteColis($id);
    }

    public function declarerEnvoi($idRecu)
    {
        exigerRole(['citoyen']);
        $utilisateur = utilisateurConnecte();
        $idBureau = $this->identifiantValide($idRecu);
        if ($idBureau === null || !(new BureauPostalController($this->connexion))->recupererBureau($idBureau)) {
            http_response_code(422);
            return tr('Choisissez un bureau de départ dans la liste.', 'اختر مكتب الإرسال من القائمة.');
        }

        try {
            $colis = $this->ajouterColis($idBureau, $utilisateur['id']);
        } catch (PDOException $erreur) {
            if ((new BureauPostalController($this->connexion))->recupererBureau($idBureau) !== null) {
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
        return $this->listeColis($utilisateur['id']);
    }

    public function annulerEnvoi($idRecu)
    {
        exigerRole(['citoyen']);
        $utilisateur = utilisateurConnecte();
        $idColis = $this->identifiantValide($idRecu);
        if ($idColis !== null && $this->annulerColis($idColis, $utilisateur['id'])) {
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

    public function index()
    {
        $query = posteText($_GET, 'q');
        $statuses = ColisController::STATUTS;
        $statusFilter = posteText($_GET, 'statut');
        if (!in_array($statusFilter, $statuses, true)) {
            $statusFilter = '';
        }

        $parcels = [];
        foreach ($this->listeColis() as $parcel) {
            $matchesStatus = $statusFilter === '' || $parcel['statut'] === $statusFilter;
            $parcelInfo = $parcel['numero_suivi'] . ' ' . $parcel['bureau'] . ' ' . $parcel['ville'];
            if ($matchesStatus && posteMatches($query, $parcelInfo)) {
                $parcels[] = $parcel;
            }
        }

        $titre = ($query !== '' || $statusFilter !== '') ? 'Rechercher un colis' : 'Gestion des colis';
        posteRender('colisList', $titre, 'colis', [
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
            $parcel = $this->find(posteId($_GET));
        }
        if ($parcel !== null) {
            $this->renderForm($parcel);
        }
    }

    public function save()
    {
        if (!posteCsrfValid()) {
            return;
        }

        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $this->saveStatus();
            return;
        }

        $officeId = posteId($_POST, 'id_bureau_depart');
        $citizenId = posteId($_POST, 'id_citoyen');
        $parcel = [
            'id_bureau_depart' => posteText($_POST, 'id_bureau_depart'),
            'id_citoyen' => posteText($_POST, 'id_citoyen'),
        ];
        $errors = [];
        if ($officeId === null || (new BureauPostalController($this->connexion))->recupererBureau($officeId) === null) {
            $errors['id_bureau_depart'] = 'Choisissez un bureau de départ disponible.';
        }

        $citizenFound = false;
        foreach ($this->listeCitoyens() as $citizen) {
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
            $parcel = $this->ajouterColis($officeId, $citizenId);
        } catch (PDOException $error) {
            // Le bureau peut avoir été supprimé après l’ouverture du formulaire.
            if ($error->getCode() !== '23000') {
                throw $error;
            }
            $this->renderForm($parcel, ['id_bureau_depart' => 'Vérifiez le bureau de départ et le compte citoyen.']);
            return;
        }
        posteRedirect('colis', 'Le colis ' . $parcel['numero_suivi'] . ' a été enregistré.');
    }

    public function accepter()
    {
        if (!posteCsrfValid()) {
            return;
        }
        $id = posteId($_POST);
        $parcel = $this->find($id);
        if ($parcel === null) {
            return;
        }
        if (!$this->modifierColis($id, 'Accepté', 'En attente')) {
            posteProblem(409, 'Déclaration déjà traitée', 'Ce colis n’est plus en attente. Rechargez la liste des colis.', 'colis');
            return;
        }
        posteRedirect('colis', 'Le colis ' . $parcel['numero_suivi'] . ' a été accepté.');
    }

    public function confirmDelete()
    {
        $parcel = $this->find(posteId($_GET));
        if ($parcel !== null) {
            posteRender('supprimer', 'Supprimer le colis', 'colis', [
                'id' => $parcel['id_colis'],
                'reference' => $parcel['numero_suivi'],
                'action' => 'deleteColis',
                'message' => 'Le colis sera retiré du suivi et de l’historique citoyen. Cette action est définitive.',
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
        if (!$this->deleteColis($id)) {
            posteRedirect('colis', 'Ce colis a déjà été supprimé.', 'warning');
        }
        posteRedirect('colis', 'Le colis a été supprimé.');
    }

    private function saveStatus()
    {
        $id = posteId($_POST);
        $parcel = $this->find($id);
        if ($parcel === null) {
            return;
        }

        if (posteText($_POST, 'statut_actuel') !== $parcel['statut']) {
            $this->renderForm($parcel, ['statut' => 'Le statut a changé depuis l’ouverture du formulaire. Vérifiez son état actuel.'], 409);
            return;
        }
        $status = posteText($_POST, 'statut');
        if (!in_array($status, ColisController::statutsSuivants($parcel['statut']), true)) {
            $this->renderForm($parcel, ['statut' => 'Choisissez une étape proposée pour ce colis.']);
            return;
        }
        if (!$this->modifierColis($id, $status, $parcel['statut'])) {
            $parcel = $this->find($id);
            if ($parcel !== null) {
                $this->renderForm($parcel, ['statut' => 'Le colis vient d’être modifié. Vérifiez son état avant de réessayer.'], 409);
            }
            return;
        }

        posteRedirect('colis', 'Le statut du colis a été mis à jour.');
    }

    private function find($id)
    {
        $parcel = null;
        if ($id !== null) {
            $parcel = $this->recupererColis($id);
        }
        if ($parcel === null) {
            posteProblem(404, 'Colis introuvable', 'Ce colis n’existe plus ou le lien est incorrect.', 'colis');
        }
        return $parcel;
    }

    private function renderForm($parcel, $errors = [], $code = 422)
    {
        if ($errors !== []) {
            http_response_code($code);
        }

        $editing = !empty($parcel['id_colis']);
        $title = 'Modifier le statut';
        if ($editing && ColisController::statutsSuivants($parcel['statut']) === []) {
            $title = 'Consulter le colis';
        }
        $offices = [];
        $citizens = [];
        if (!$editing) {
            $title = 'Enregistrer un colis';
                $offices = (new BureauPostalController($this->connexion))->listeBureaux();
            $citizens = $this->listeCitoyens();
        }
        posteRender('colisForm', $title, 'colis', [
            'parcel' => $parcel,
            'editing' => $editing,
            'statuses' => $editing ? ColisController::statutsSuivants($parcel['statut']) : [],
            'offices' => $offices,
            'citizens' => $citizens,
            'errors' => $errors,
        ]);
    }

    public function statistiques()
    {
        exigerRole(['admin']);
        $colisC = $this;
        $bureauC = new BureauPostalController();
        $parcels = $colisC->listeColis();
        $statusCounts = [];
        foreach (ColisController::STATUTS as $status) {
            $statusCounts[$status] = 0;
        }
        foreach ($parcels as $parcel) {
            $statusCounts[$parcel['statut']]++;
        }

        $stats = [
            ['label' => 'Colis enregistrés', 'value' => count($parcels), 'icon' => 'box'],
            ['label' => 'En attente', 'value' => $statusCounts['En attente'], 'icon' => 'clock'],
            ['label' => 'Acceptés', 'value' => $statusCounts['Accepté'], 'icon' => 'check-circle'],
            ['label' => 'En transit', 'value' => $statusCounts['En transit'], 'icon' => 'truck'],
            ['label' => 'Bureaux de poste', 'value' => count($bureauC->listeBureaux()), 'icon' => 'geo-alt'],
        ];

        $stats[] = ['label' => 'Utilisateurs', 'value' => count(gestionUtilisateurs()->lister()), 'icon' => 'people'];

        return ['stats' => $stats, 'parcels' => array_slice($parcels, 0, 5), 'statusCounts' => $statusCounts];
    }

    public function suiviFront(): void {
        $rechercheLancee = isset($_GET['numero']);
        $numeroSuivi = isset($_GET['numero']) && is_string($_GET['numero']) ? strtoupper(trim($_GET['numero'])) : '';
        $numeroValide = preg_match('/^ET[0-9A-Z]{6,20}TN$/', $numeroSuivi) === 1;
        $colis = $numeroValide ? $this->chercherColis($numeroSuivi) : null;
        posteFront('suiviColis', tr('Suivre un colis', 'تتبّع طرد'), compact('rechercheLancee', 'numeroSuivi', 'numeroValide', 'colis'));
    }

    public function declarerFront(): void {
        exigerRole(['citoyen']);
        $erreur = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $erreur = $this->declarerEnvoi($_POST['id_bureau_depart'] ?? '');
        posteFront('declarerEnvoi', tr('Déclarer un envoi', 'التصريح بإرسال طرد'),
            ['erreur' => $erreur, 'bureaux' => (new BureauPostalController())->listeBureaux(),
             'modification' => false, 'bureauSelectionne' => posteText($_POST, 'id_bureau_depart')]);
    }

    public function modifierFront(): void
    {
        $utilisateur = exigerRole(['citoyen']);
        $colis = $this->trouverDeclarationCitoyen($utilisateur['id']);
        if ($colis === null) return;

        $erreur = '';
        $bureauSelectionne = (string) $colis['id_bureau_depart'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bureauSelectionne = posteText($_POST, 'id_bureau_depart');
            $idBureau = posteId($_POST, 'id_bureau_depart');
            if ($idBureau === null || (new BureauPostalController())->recupererBureau($idBureau) === null) {
                http_response_code(422);
                $erreur = tr('Choisissez un bureau de départ dans la liste.', 'اختر مكتب الإرسال من القائمة.');
            } else {
                try {
                    if ($idBureau !== (int) $colis['id_bureau_depart']
                        && !$this->modele->modifierDeclaration($colis['id_colis'], $utilisateur['id'], $idBureau)) {
                        http_response_code(409);
                        $this->afficherMesColis(tr('Ce colis vient d’être traité ou supprimé. Rechargez la liste.', 'تمّت معالجة هذا الطرد أو حذفه. أعد تحميل القائمة.'));
                        return;
                    }
                    $this->retourHistorique(['kind' => 'updated']);
                } catch (PDOException $exception) {
                    if ($exception->getCode() !== '23000') throw $exception;
                    http_response_code(409);
                    $erreur = tr('Ce bureau n’est plus disponible. Choisissez un autre bureau.', 'لم يعد هذا المكتب متاحًا. اختر مكتبًا آخر.');
                }
            }
        }

        posteFront('declarerEnvoi', tr('Modifier mon envoi', 'تعديل إرساليتي'), [
            'erreur' => $erreur, 'bureaux' => (new BureauPostalController())->listeBureaux(),
            'modification' => true, 'colis' => $colis, 'bureauSelectionne' => $bureauSelectionne,
        ]);
    }

    public function supprimerFront(): void
    {
        $utilisateur = exigerRole(['citoyen']);
        $colis = $this->trouverDeclarationCitoyen($utilisateur['id']);
        if ($colis === null) return;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->modele->supprimerDeclaration($colis['id_colis'], $utilisateur['id'])) {
                http_response_code(409);
                $this->afficherMesColis(tr('Ce colis vient d’être traité ou supprimé. Rechargez la liste.', 'تمّت معالجة هذا الطرد أو حذفه. أعد تحميل القائمة.'));
                return;
            }
            $this->retourHistorique(['kind' => 'deleted']);
        }

        $this->afficherMesColis('', $colis);
    }

    private function trouverDeclarationCitoyen($idCitoyen)
    {
        $id = posteId($_GET);
        $colis = $id === null ? null : $this->modele->recupererColisCitoyen($id, $idCitoyen);
        if ($colis === null) {
            http_response_code(404);
            $this->afficherMesColis(tr('Colis introuvable dans vos déclarations.', 'لم يتمّ العثور على الطرد في تصريحاتك.'));
            return null;
        }
        if ($colis['statut'] !== 'En attente') {
            http_response_code(409);
            $this->afficherMesColis(tr('Seuls vos colis en attente peuvent être modifiés ou supprimés.', 'يمكن تعديل طرودك قيد الانتظار أو حذفها فقط.'));
            return null;
        }
        return $colis;
    }

    public function mesColisFront(): void {
        exigerRole(['citoyen']);
        $erreur = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $erreur = $this->annulerEnvoi($_POST['id_colis'] ?? '');
        $this->afficherMesColis($erreur);
    }

    private function afficherMesColis($erreur = '', $colisASupprimer = null)
    {
        $colisDuCitoyen = $this->listerHistorique();
        $confirmation = $_SESSION['poste_front_flash'] ?? null;
        unset($_SESSION['poste_front_flash']);
        posteFront('mesColis', tr('Mes colis', 'طرودي'), compact('erreur', 'colisDuCitoyen', 'confirmation', 'colisASupprimer'));
    }
}
