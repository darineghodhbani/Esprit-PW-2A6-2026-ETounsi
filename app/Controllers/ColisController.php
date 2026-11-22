<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/BureauPostalController.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Colis.php';

class ColisController extends Controller {
    private $modele;
    public const STATUTS = Colis::STATUTS;

    public function __construct() {
        $this->modele = new Colis();
    }

    public function listerColis() {
        return $this->modele->listerColis();
    }

    public function listerColisCitoyen($idCitoyen) {
        return $this->modele->listerColisCitoyen($idCitoyen);
    }

    public function recupererColis($id) {
        return $this->modele->recupererColis($id);
    }

    public function recupererColisCitoyen($id, $idCitoyen) {
        return $this->modele->recupererColisCitoyen($id, $idCitoyen);
    }

    public function chercherColis($numero) {
        return $this->modele->chercherColis($numero);
    }

    public function listerCitoyens() {
        return $this->modele->listerCitoyens();
    }

    public function ajouterColis(Colis $colis) {
        return $this->modele->ajouterColis($colis);
    }

    public function modifierColis(Colis $colis, $id, $statutActuel) {
        return $this->modele->modifierColis($colis, $id, $statutActuel);
    }

    public function modifierColisCitoyen(Colis $colis, $id) {
        return $this->modele->modifierColisCitoyen($colis, $id);
    }

    public function supprimerColis($id) {
        return $this->modele->supprimerColis($id);
    }

    public function supprimerColisCitoyen($id, $idCitoyen) {
        return $this->modele->supprimerColisCitoyen($id, $idCitoyen);
    }

    public function annulerColis($id, $idCitoyen) {
        return $this->modele->annulerColis($id, $idCitoyen);
    }

    public static function statutsSuivants($statut) {
        return Colis::statutsSuivants($statut);
    }

    public static function clean(array $post): array {
        return [
            'id_bureau_depart' => posteText($post, 'id_bureau_depart'),
            'id_citoyen' => posteText($post, 'id_citoyen'),
            'nom_citoyen' => posteText($post, 'nom_citoyen'),
            'date_declaration' => posteText($post, 'date_declaration'),
        ];
    }

    public function verifierColis(array $d, bool $admin = false): array {
        $erreurs = [];
        $idBureau = posteId($d, 'id_bureau_depart');
        if ($idBureau === null || !(new BureauPostalController())->recupererBureau($idBureau)) {
            $erreurs['id_bureau_depart'] = t('poste.erreur.bureau_depart');
        }
        if ($admin) {
            $idCitoyen = posteId($d, 'id_citoyen');
            if ($idCitoyen === null || !in_array($idCitoyen, array_column($this->listerCitoyens(), 'id_citoyen'), true)) {
                $erreurs['id_citoyen'] = 'Choisissez un compte citoyen disponible.';
            }
        } else {
            if (mb_strlen($d['nom_citoyen']) < 2 || mb_strlen($d['nom_citoyen']) > 200
                || preg_match('/[\x00-\x1F\x7F<>]/u', $d['nom_citoyen'])) {
                $erreurs['nom_citoyen'] = t('poste.erreur.nom_citoyen');
            }
            $dateValide = preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/D', $d['date_declaration'], $parties) === 1
                && (int) $parties[1] >= 1000 && checkdate((int) $parties[2], (int) $parties[3], (int) $parties[1]);
            if (!$dateValide) $erreurs['date_declaration'] = t('poste.erreur.date_declaration');
        }
        return $erreurs;
    }

    public function declarer(): void {
        exigerRole(['citoyen']);
        $idCitoyen = (int) $_SESSION['id_citoyen'];
        $erreurs = [];
        $old = self::clean([]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $erreurs = $this->verifierColis($old);
            if (count($erreurs) == 0) {
                $colis = new Colis(null, (int) $old['id_bureau_depart'], $idCitoyen, null, null, $old['nom_citoyen'], $old['date_declaration']);
                try {
                    $resultat = $this->ajouterColis($colis);
                    $_SESSION['flash'] = ['type' => 'success', 'kind' => 'declared', 'message' => 'Envoi déclaré.', 'number' => $resultat['numero_suivi']];
                    header('Location: ' . frontUrl('mes-colis'), true, 303);
                    exit;
                } catch (PDOException $e) {
                    if ($e->getCode() !== '23000') throw $e;
                    $erreurs['id_bureau_depart'] = t('poste.erreur.bureau_indisponible');
                    http_response_code(409);
                }
            } else {
                http_response_code(422);
            }
        }
        $this->renderFront('poste/declarerEnvoi', [
            'title' => t('poste.declarer_envoi'), 'currentPage' => 'declarer-envoi',
            'old' => $old, 'erreurs' => $erreurs, 'modification' => false,
            'bureaux' => (new BureauPostalController())->listerBureaux(),
        ]);
    }

    public function mesColis(): void {
        exigerRole(['citoyen']);
        $erreur = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = posteId($_POST, 'id_colis');
            if ($id !== null && $this->annulerColis($id, (int) $_SESSION['id_citoyen'])) {
                $_SESSION['flash'] = ['type' => 'success', 'kind' => 'cancelled', 'message' => 'L’envoi a été annulé.'];
                header('Location: ' . frontUrl('mes-colis'), true, 303);
                exit;
            }
            http_response_code(409);
            $erreur = t('poste.erreur.annulation');
        }
        $this->afficherMesColis($erreur);
    }

    public function modifierFront(): void {
        exigerRole(['citoyen']);
        $idCitoyen = (int) $_SESSION['id_citoyen'];
        $colis = $this->trouverColisCitoyen($idCitoyen);
        if (!$colis) return;
        $id = (int) $colis['id_colis'];
        $old = $colis;
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id_colis'] = $id;
            $old['numero_suivi'] = $colis['numero_suivi'];
            $erreurs = $this->verifierColis($old);
            if (count($erreurs) == 0) {
                $modifie = (int) $old['id_bureau_depart'] !== (int) $colis['id_bureau_depart']
                    || $old['nom_citoyen'] !== $colis['nom_citoyen'] || $old['date_declaration'] !== $colis['date_declaration'];
                $declaration = new Colis($id, (int) $old['id_bureau_depart'], $idCitoyen, null, null, $old['nom_citoyen'], $old['date_declaration']);
                try {
                    if ($modifie && !$this->modifierColisCitoyen($declaration, $id)) {
                        http_response_code(409);
                        $this->afficherMesColis(t('poste.erreur.colis_deja_traite'));
                        return;
                    }
                    $_SESSION['flash'] = ['type' => 'success', 'kind' => 'updated', 'message' => 'Les informations de votre envoi ont été mises à jour.'];
                    header('Location: ' . frontUrl('mes-colis'), true, 303);
                    exit;
                } catch (PDOException $e) {
                    if ($e->getCode() !== '23000') throw $e;
                    http_response_code(409);
                    $erreurs['id_bureau_depart'] = t('poste.erreur.bureau_indisponible');
                }
            } else {
                http_response_code(422);
            }
        }
        $this->renderFront('poste/declarerEnvoi', [
            'title' => t('poste.modifier_mon_envoi'), 'currentPage' => 'mes-colis',
            'old' => $old, 'erreurs' => $erreurs, 'modification' => true,
            'bureaux' => (new BureauPostalController())->listerBureaux(),
        ]);
    }

    public function supprimerFront(): void {
        exigerRole(['citoyen']);
        $idCitoyen = (int) $_SESSION['id_citoyen'];
        $colis = $this->trouverColisCitoyen($idCitoyen);
        if (!$colis) return;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->supprimerColisCitoyen($colis['id_colis'], $idCitoyen)) {
                http_response_code(409);
                $this->afficherMesColis(t('poste.erreur.colis_deja_traite'));
                return;
            }
            $_SESSION['flash'] = ['type' => 'success', 'kind' => 'deleted', 'message' => 'Votre envoi a été supprimé.'];
            header('Location: ' . frontUrl('mes-colis'), true, 303);
            exit;
        }
        $this->afficherMesColis('', $colis);
    }

    public function suivi(): void {
        $rechercheLancee = isset($_GET['numero']);
        $numeroSuivi = strtoupper(posteText($_GET, 'numero'));
        $numeroValide = preg_match('/^ET[0-9A-Z]{6,20}TN$/', $numeroSuivi) === 1;
        $colis = $numeroValide ? $this->chercherColis($numeroSuivi) : null;
        $this->renderFront('poste/suiviColis', [
            'title' => t('poste.suivre_colis'), 'currentPage' => 'suivi-colis',
            'rechercheLancee' => $rechercheLancee, 'numeroSuivi' => $numeroSuivi,
            'numeroValide' => $numeroValide, 'colis' => $colis,
        ]);
    }

    public function listBack(): void {
        CitoyenController::requireAdmin();
        $recherche = posteText($_GET, 'q');
        $filtreStatut = posteText($_GET, 'statut');
        if (!in_array($filtreStatut, self::STATUTS, true)) $filtreStatut = '';
        $liste = [];
        foreach ($this->listerColis() as $colis) {
            $texte = $colis['numero_suivi'] . ' ' . $colis['bureau'] . ' ' . $colis['ville'] . ' ' . $colis['nom_citoyen'];
            if (($filtreStatut === '' || $colis['statut'] === $filtreStatut) && posteMatches($recherche, $texte)) $liste[] = $colis;
        }
        $this->renderBack('poste/colisList', [
            'title' => 'Gestion des colis', 'currentPage' => 'admin-colis', 'section' => 'colis',
            'liste' => $liste, 'recherche' => $recherche, 'filtreStatut' => $filtreStatut, 'statuts' => self::STATUTS,
        ]);
    }

    public function addBack(): void {
        CitoyenController::requireAdmin();
        $erreurs = [];
        $old = self::clean([]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $erreurs = $this->verifierColis($old, true);
            if (count($erreurs) == 0) {
                $colis = new Colis(null, (int) $old['id_bureau_depart'], (int) $old['id_citoyen']);
                try {
                    $resultat = $this->ajouterColis($colis);
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le colis ' . $resultat['numero_suivi'] . ' a été enregistré.'];
                    header('Location: index.php?page=admin-colis', true, 303);
                    exit;
                } catch (PDOException $e) {
                    if ($e->getCode() !== '23000') throw $e;
                    $erreurs['id_bureau_depart'] = 'Vérifiez le bureau de départ et le compte citoyen.';
                    http_response_code(409);
                }
            } else {
                http_response_code(422);
            }
        }
        $this->renderBack('poste/addColis', [
            'title' => 'Enregistrer un colis', 'currentPage' => 'admin-colis', 'section' => 'colis',
            'old' => $old, 'erreurs' => $erreurs, 'statuts' => [],
            'bureaux' => (new BureauPostalController())->listerBureaux(), 'citoyens' => $this->listerCitoyens(),
        ]);
    }

    public function updateBack(): void {
        CitoyenController::requireAdmin();
        $id = posteId($_GET) ?? posteId($_POST);
        $old = $id === null ? null : $this->recupererColis($id);
        if (!$old) { $this->introuvable(); return; }
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $statut = posteText($_POST, 'statut');
            if (posteText($_POST, 'statut_actuel') !== $old['statut']) {
                http_response_code(409);
                $erreurs['statut'] = 'Le statut a changé depuis l’ouverture du formulaire. Vérifiez son état actuel.';
            } elseif (!in_array($statut, self::statutsSuivants($old['statut']), true)) {
                http_response_code(422);
                $erreurs['statut'] = 'Choisissez une étape proposée pour ce colis.';
            } else {
                $colis = new Colis($id, null, null, null, $statut);
                if ($this->modifierColis($colis, $id, $old['statut'])) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le statut du colis a été mis à jour.'];
                    header('Location: index.php?page=admin-colis', true, 303);
                    exit;
                }
                http_response_code(409);
                $erreurs['statut'] = 'Ce colis vient d’être modifié. Rechargez son état avant de réessayer.';
                $old = $this->recupererColis($id);
                if (!$old) { $this->introuvable(); return; }
            }
        }
        $this->renderBack('poste/updateColis', [
            'title' => 'Modifier le statut', 'currentPage' => 'admin-colis', 'section' => 'colis',
            'old' => $old, 'erreurs' => $erreurs, 'statuts' => self::statutsSuivants($old['statut']),
            'bureaux' => [], 'citoyens' => [],
        ]);
    }

    public function deleteBack(): void {
        CitoyenController::requireAdmin();
        $id = $_SERVER['REQUEST_METHOD'] === 'POST' ? posteId($_POST) : posteId($_GET);
        $colis = $id === null ? null : $this->recupererColis($id);
        if (!$colis) { $this->introuvable(); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->supprimerColis($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le colis a été supprimé.'];
            header('Location: index.php?page=admin-colis', true, 303);
            exit;
        }
        $this->renderBack('commun/supprimer', [
            'title' => 'Supprimer le colis', 'currentPage' => 'admin-colis', 'section' => 'colis', 'erreurs' => [],
            'old' => ['id' => $id, 'reference' => $colis['numero_suivi'], 'action' => 'deleteColis',
                'message' => 'Le colis sera retiré du suivi et de l’historique citoyen. Cette action est définitive.'],
        ]);
    }

    public function accepter(): void {
        CitoyenController::requireAdmin();
        $id = posteId($_POST);
        $old = $id === null ? null : $this->recupererColis($id);
        if (!$old) { $this->introuvable(); return; }
        $colis = new Colis($id, null, null, null, 'Accepté');
        if (!$this->modifierColis($colis, $id, 'En attente')) {
            http_response_code(409);
            $this->renderBack('commun/problem', [
                'title' => 'Déclaration déjà traitée', 'currentPage' => 'admin-colis', 'section' => 'colis',
                'message' => 'Ce colis n’est plus en attente. Rechargez la liste des colis.',
            ]);
            return;
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le colis ' . $old['numero_suivi'] . ' a été accepté.'];
        header('Location: index.php?page=admin-colis', true, 303);
        exit;
    }

    public function statistiques(): array {
        CitoyenController::requireAdmin();
        $liste = $this->listerColis();
        $compteurs = array_fill_keys(self::STATUTS, 0);
        foreach ($liste as $colis) $compteurs[$colis['statut']]++;
        return [
            'stats' => [
                ['label' => 'Colis enregistrés', 'value' => count($liste), 'icon' => 'box'],
                ['label' => 'En attente', 'value' => $compteurs['En attente'], 'icon' => 'clock'],
                ['label' => 'Acceptés', 'value' => $compteurs['Accepté'], 'icon' => 'check-circle'],
                ['label' => 'En transit', 'value' => $compteurs['En transit'], 'icon' => 'truck'],
                ['label' => 'Bureaux de poste', 'value' => (new BureauPostalController())->compterBureaux(), 'icon' => 'geo-alt'],
                ['label' => 'Utilisateurs', 'value' => count(gestionUtilisateurs()->lister()), 'icon' => 'people'],
            ],
            'liste' => array_slice($liste, 0, 5), 'compteurs' => $compteurs,
        ];
    }

    private function trouverColisCitoyen($idCitoyen) {
        $id = posteId($_GET);
        $colis = $id === null ? null : $this->recupererColisCitoyen($id, $idCitoyen);
        if (!$colis) {
            http_response_code(404);
            $this->afficherMesColis(t('poste.erreur.colis_introuvable'));
            return null;
        }
        if ($colis['statut'] !== 'En attente') {
            http_response_code(409);
            $this->afficherMesColis(t('poste.erreur.colis_en_attente'));
            return null;
        }
        return $colis;
    }

    private function afficherMesColis($erreur = '', $colisASupprimer = null): void {
        $confirmation = isset($_SESSION['flash']['kind']) ? $_SESSION['flash'] : null;
        unset($_SESSION['flash']);
        $this->renderFront('poste/mesColis', [
            'title' => t('poste.mes_colis'), 'currentPage' => 'mes-colis', 'erreur' => $erreur,
            'liste' => $this->listerColisCitoyen((int) $_SESSION['id_citoyen']),
            'confirmation' => $confirmation, 'colisASupprimer' => $colisASupprimer,
        ]);
    }

    private function introuvable(): void {
        http_response_code(404);
        $this->renderBack('commun/problem', [
            'title' => 'Colis introuvable', 'currentPage' => 'admin-colis', 'section' => 'colis',
            'message' => 'Ce colis n’existe plus ou le lien est incorrect.',
        ]);
    }
}
