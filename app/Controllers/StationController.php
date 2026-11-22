<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Station.php';
require_once __DIR__ . '/LigneTransportController.php';

class StationController extends Controller {

    // ==============================================================
    // REQUÊTES SQL
    // ==============================================================

    // Stations avec le numéro, le nom et le tarif de leur ligne. $idLigne = 0 : toutes les lignes.
    public function listerStations($idLigne = 0, $recherche = '', $lignesActivesSeulement = false) {
        $sql = "SELECT s.*, l.numero, l.nom AS ligne_nom, l.tarif, l.actif
                FROM station s
                INNER JOIN ligne_transport l ON l.id_ligne = s.id_ligne
                WHERE 1 = 1";
        $parametres = [];
        if ((int) $idLigne > 0) {
            $sql .= " AND s.id_ligne = :id_ligne";
            $parametres['id_ligne'] = (int) $idLigne;
        }
        if ($recherche !== '') {
            $sql .= " AND (s.nom LIKE :motif1 OR s.adresse LIKE :motif2)";
            $parametres['motif1'] = '%' . $recherche . '%';
            $parametres['motif2'] = '%' . $recherche . '%';
        }
        if ($lignesActivesSeulement) {
            $sql .= " AND l.actif = 1";
        }
        $sql .= " ORDER BY l.numero, s.ordre, s.nom";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute($parametres);
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function recupererStation($id) {
        $sql = "SELECT * FROM station WHERE id_station = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $station = $query->fetch();
            return $station === false ? null : $station;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Compteurs du Back Office : stations, lignes desservies.
    public function statistiquesStations() {
        $sql = "SELECT COUNT(*) AS total, COUNT(DISTINCT id_ligne) AS lignes FROM station";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetch();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Deux stations d'une même ligne ne partagent pas la même position.
    public function ordreOccupe($idLigne, $ordre, $exclureId = null) {
        $sql = "SELECT COUNT(*) FROM station WHERE id_ligne = :id_ligne AND ordre = :ordre AND id_station <> :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_ligne' => $idLigne, 'ordre' => $ordre, 'id' => $exclureId ?? 0]);
            return (int) $query->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function ajouterStation(Station $station) {
        $sql = "INSERT INTO station (nom, adresse, ordre, id_ligne) VALUES (:nom, :adresse, :ordre, :id_ligne)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $station->getNom(),
                'adresse' => $station->getAdresse(),
                'ordre' => $station->getOrdre(),
                'id_ligne' => $station->getIdLigne(),
            ]);
            return (int) $db->lastInsertId();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function modifierStation(Station $station, $id) {
        $sql = "UPDATE station SET nom = :nom, adresse = :adresse, ordre = :ordre, id_ligne = :id_ligne WHERE id_station = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $station->getNom(),
                'adresse' => $station->getAdresse(),
                'ordre' => $station->getOrdre(),
                'id_ligne' => $station->getIdLigne(),
                'id' => $id,
            ]);
            return $query->rowCount() === 1 || $this->recupererStation($id) !== null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function supprimerStation($id) {
        $sql = "DELETE FROM station WHERE id_station = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount() === 1;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // ==============================================================
    // VALIDATION
    // ==============================================================

    public static function clean(array $post): array {
        return [
            'id_ligne' => posteText($post, 'id_ligne'),
            'nom' => posteText($post, 'nom'),
            'adresse' => posteText($post, 'adresse'),
            'ordre' => posteText($post, 'ordre'),
        ];
    }

    public function verifierStation(array $d, $id = null): array {
        $erreurs = [];
        $ligneC = new LigneTransportController();
        if (!ctype_digit($d['id_ligne']) || $ligneC->recupererLigne((int) $d['id_ligne']) === null) {
            $erreurs['id_ligne'] = 'Choisissez une ligne existante.';
        }
        if ($d['nom'] === '') {
            $erreurs['nom'] = 'Le nom de la station est obligatoire.';
        } elseif (mb_strlen($d['nom']) > 100) {
            $erreurs['nom'] = 'Le nom doit contenir au maximum 100 caractères.';
        }
        if (mb_strlen($d['adresse']) > 255) {
            $erreurs['adresse'] = 'L’adresse doit contenir au maximum 255 caractères.';
        }
        if (!ctype_digit($d['ordre']) || (int) $d['ordre'] < 1 || (int) $d['ordre'] > 999) {
            $erreurs['ordre'] = 'L’ordre de passage doit être un entier entre 1 et 999.';
        } elseif (!isset($erreurs['id_ligne']) && $this->ordreOccupe((int) $d['id_ligne'], (int) $d['ordre'], $id)) {
            $erreurs['ordre'] = 'Une autre station occupe déjà cette position sur la ligne.';
        }
        return $erreurs;
    }

    private function nouvelleStation(array $d, $id = null): Station {
        $adresse = $d['adresse'] === '' ? null : $d['adresse'];
        return new Station($id, $d['nom'], $adresse, (int) $d['ordre'], (int) $d['id_ligne']);
    }

    private static function retour($idLigne): string {
        return 'index.php?page=admin-stations' . ((int) $idLigne > 0 ? '&ligne=' . (int) $idLigne : '');
    }

    // ==============================================================
    // BACK OFFICE
    // ==============================================================

    public function listBack(): void {
        CitoyenController::requireAdmin();

        $filtre = (int) posteText($_GET, 'ligne');
        $this->renderBack('transport/stationList', [
            'title' => 'E-Tounsi Admin - Stations',
            'currentPage' => 'admin-stations',
            'section' => 'admin-stations',
            'csrfToken' => csrfToken(),
            'liste' => $this->listerStations($filtre),
            'lignes' => (new LigneTransportController())->listerLignes(),
            'filtre' => $filtre,
            'stats' => $this->statistiquesStations(),
        ]);
    }

    public function addBack(): void {
        CitoyenController::requireAdmin();

        $erreurs = [];
        $old = ['id_ligne' => (string) (int) posteText($_GET, 'ligne'), 'nom' => '', 'adresse' => '', 'ordre' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierStation($old);
            }
            if (count($erreurs) == 0) {
                $this->ajouterStation($this->nouvelleStation($old));
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'La station a été ajoutée.'];
                header('Location: ' . self::retour($old['id_ligne']), true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $this->renderBack('transport/addStation', [
            'title' => 'Ajouter une station',
            'currentPage' => 'admin-stations',
            'section' => 'admin-stations',
            'csrfToken' => csrfToken(),
            'old' => $old,
            'erreurs' => $erreurs,
            'lignes' => (new LigneTransportController())->listerLignes(),
        ]);
    }

    public function updateBack(): void {
        CitoyenController::requireAdmin();

        $id = posteId($_GET) ?? posteId($_POST);
        $station = $id === null ? null : $this->recupererStation($id);
        if (!$station) {
            http_response_code(404);
            $this->renderBack('commun/problem', ['title' => 'Station introuvable', 'currentPage' => 'admin-stations',
                'section' => 'admin-stations', 'message' => 'Cet élément n’existe plus ou le lien est incorrect.']);
            return;
        }

        $erreurs = [];
        $old = [
            'id_ligne' => (string) $station['id_ligne'],
            'nom' => $station['nom'],
            'adresse' => (string) ($station['adresse'] ?? ''),
            'ordre' => (string) $station['ordre'],
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierStation($old, $id);
            }
            if (count($erreurs) == 0) {
                $this->modifierStation($this->nouvelleStation($old, $id), $id);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'La station a été mise à jour.'];
                header('Location: ' . self::retour($old['id_ligne']), true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $this->renderBack('transport/updateStation', [
            'title' => 'Modifier une station',
            'currentPage' => 'admin-stations',
            'section' => 'admin-stations',
            'csrfToken' => csrfToken(),
            'id' => $id,
            'old' => $old,
            'erreurs' => $erreurs,
            'lignes' => (new LigneTransportController())->listerLignes(),
        ]);
    }

    public function deleteBack(): void {
        CitoyenController::requireAdmin();

        $retour = (int) posteText($_POST, 'ligne');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {
            $id = posteId($_POST);
            $supprime = $id !== null && $this->supprimerStation($id);
            $_SESSION['flash'] = [
                'type' => $supprime ? 'success' : 'warning',
                'message' => $supprime ? 'La station a été supprimée.' : 'Cette station n’existe plus.',
            ];
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
        }

        header('Location: ' . self::retour($retour), true, 303);
        exit;
    }

    // ==============================================================
    // FRONT OFFICE : stations et tarifs des lignes actives
    // ==============================================================

    public function listFront(): void {
        $lignes = (new LigneTransportController())->listerLignes('', true);
        $filtre = (int) posteText($_GET, 'ligne');
        if (!in_array($filtre, array_map('intval', array_column($lignes, 'id_ligne')), true)) {
            $filtre = 0;
        }
        $recherche = mb_substr(posteText($_GET, 'q'), 0, 100);

        $this->renderFront('transport/stations', [
            'title' => tr('Stations et tarifs', 'المحطات والتعريفات'),
            'currentPage' => 'stations-transport',
            'lignes' => $lignes,
            'filtre' => $filtre,
            'recherche' => $recherche,
            'stations' => $this->listerStations($filtre, $recherche, true),
        ]);
    }
}
