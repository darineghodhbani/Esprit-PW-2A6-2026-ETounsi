<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/BureauPostalController.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Colis.php';

class ColisController extends Controller {
    public const STATUTS = Colis::STATUTS;

    public function listerColis() {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau
                ORDER BY c.created_at DESC, c.id_colis DESC";
        $db = Database::getConnection();
        return $db->query($sql)->fetchAll();
    }

    public function compterColis($statut = null) {
        $db = Database::getConnection();
        if ($statut === null) {
            $sql = "SELECT COUNT(*) FROM colis";
            return (int) $db->query($sql)->fetchColumn();
        }
        $sql = "SELECT COUNT(*) FROM colis WHERE statut = :statut";
        $query = $db->prepare($sql);
        $query->execute(['statut' => $statut]);
        return (int) $query->fetchColumn();
    }

    public function listerColisCitoyen($idCitoyen) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau
                WHERE c.id_citoyen = :id_citoyen ORDER BY c.created_at DESC, c.id_colis DESC";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id_citoyen' => $idCitoyen]);
        return $query->fetchAll();
    }

    public function recupererColis($id) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau WHERE c.id_colis = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        $colis = $query->fetch();
        return $colis === false ? null : $colis;
    }

    public function recupererColisCitoyen($id, $idCitoyen) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau
                WHERE c.id_colis = :id AND c.id_citoyen = :id_citoyen";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id, 'id_citoyen' => $idCitoyen]);
        $colis = $query->fetch();
        return $colis === false ? null : $colis;
    }

    public function chercherColis($numero) {
        $sql = "SELECT c.*, b.nom AS bureau, b.ville FROM colis c
                JOIN bureau_postal b ON c.id_bureau_depart = b.id_bureau WHERE c.numero_suivi = :numero";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['numero' => strtoupper(trim($numero))]);
        $colis = $query->fetch();
        return $colis === false ? null : $colis;
    }

    public function listerCitoyens() {
        $sql = "SELECT id_citoyen, nom, prenom FROM citoyen WHERE role = 'citoyen' AND actif = 1 ORDER BY nom";
        $db = Database::getConnection();
        return $db->query($sql)->fetchAll();
    }

    public function ajouterColis(Colis $colis) {
        $sql = "INSERT INTO colis (id_bureau_depart, id_citoyen, numero_suivi, statut, nom_citoyen, date_declaration)
                VALUES (:id_bureau, :id_citoyen, :numero, :statut, :nom, :date_declaration)";
        $db = Database::getConnection();
        if ($colis->getNomCitoyen() === null) {
            $query = $db->prepare('SELECT nom, prenom FROM citoyen WHERE id_citoyen = :id');
            $query->execute(['id' => $colis->getIdCitoyen()]);
            $citoyen = $query->fetch();
            $colis->setNomCitoyen(trim(($citoyen['prenom'] ?? '') . ' ' . ($citoyen['nom'] ?? '')));
        }
        if ($colis->getDateDeclaration() === null) $colis->setDateDeclaration(date('Y-m-d'));
        $colis->setStatut('En attente');
        $db->beginTransaction();
        try {
            $query = $db->prepare($sql);
            do {
                $colis->setNumeroSuivi('ET' . strtoupper(bin2hex(random_bytes(5))) . 'TN');
                try {
                    $query->execute([
                        'id_bureau' => $colis->getIdBureau(), 'id_citoyen' => $colis->getIdCitoyen(),
                        'numero' => $colis->getNumeroSuivi(), 'statut' => $colis->getStatut(),
                        'nom' => $colis->getNomCitoyen(), 'date_declaration' => $colis->getDateDeclaration(),
                    ]);
                    $enregistre = true;
                } catch (PDOException $e) {
                    if (!isset($e->errorInfo[1]) || (int) $e->errorInfo[1] !== 1062) throw $e;
                    $enregistre = false;
                }
            } while (!$enregistre);
            $colis->setId((int) $db->lastInsertId());
            $resultat = $this->recupererColis($colis->getId());
            $db->commit();
            return $resultat;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function modifierColis(Colis $colis, $id, $statutActuel) {
        if (!in_array($colis->getStatut(), self::statutsSuivants($statutActuel), true)) return false;
        $sql = "UPDATE colis SET statut = :statut WHERE id_colis = :id AND statut = :statut_actuel";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['statut' => $colis->getStatut(), 'id' => $id, 'statut_actuel' => $statutActuel]);
        return $query->rowCount() === 1;
    }

    public function modifierColisCitoyen(Colis $colis, $id) {
        $sql = "UPDATE colis SET id_bureau_depart = :id_bureau, nom_citoyen = :nom, date_declaration = :date_declaration
                WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute([
            'id_bureau' => $colis->getIdBureau(), 'nom' => $colis->getNomCitoyen(),
            'date_declaration' => $colis->getDateDeclaration(), 'id' => $id,
            'id_citoyen' => $colis->getIdCitoyen(), 'statut' => 'En attente',
        ]);
        return $query->rowCount() === 1;
    }

    public function supprimerColis($id) {
        $sql = "DELETE FROM colis WHERE id_colis = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->rowCount() === 1;
    }

    public function supprimerColisCitoyen($id, $idCitoyen) {
        $sql = "DELETE FROM colis WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :statut";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id, 'id_citoyen' => $idCitoyen, 'statut' => 'En attente']);
        return $query->rowCount() === 1;
    }

    public function annulerColis($id, $idCitoyen) {
        $sql = "UPDATE colis SET statut = :annule WHERE id_colis = :id AND id_citoyen = :id_citoyen AND statut = :attente";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['annule' => 'Annulé', 'id' => $id, 'id_citoyen' => $idCitoyen, 'attente' => 'En attente']);
        return $query->rowCount() === 1;
    }

    public static function statutsSuivants($statut) {
        $transitions = [
            'En attente' => ['Accepté', 'Annulé'],
            'Accepté' => ['En transit', 'Annulé'],
            'En transit' => ['Livré'],
        ];
        return $transitions[$statut] ?? [];
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
        $this->renderBack('poste/colisList');
    }

    public function addBack(): void {
        $this->renderBack('poste/addColis');
    }

    public function updateBack(): void {
        $this->renderBack('poste/updateColis');
    }

    public function deleteBack(): void {
        require __DIR__ . '/../Views/backoffice/pages/poste/deleteColis.php';
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
