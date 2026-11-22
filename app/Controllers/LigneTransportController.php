<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/LigneTransport.php';
require_once __DIR__ . '/StationController.php';
require_once __DIR__ . '/HoraireController.php';

class LigneTransportController extends Controller {

    // ==============================================================
    // REQUÊTES SQL
    // ==============================================================

    // Lignes avec leur nombre de stations et d'horaires. $recherche filtre le numéro ou le nom.
    public function listerLignes($recherche = '', $actifSeulement = false) {
        $sql = "SELECT l.*,
                       (SELECT COUNT(*) FROM station s WHERE s.id_ligne = l.id_ligne) AS nb_stations,
                       (SELECT COUNT(*) FROM horaire h WHERE h.id_ligne = l.id_ligne) AS nb_horaires
                FROM ligne_transport l
                WHERE 1 = 1";
        $parametres = [];
        if ($recherche !== '') {
            $sql .= " AND (l.numero LIKE :motif1 OR l.nom LIKE :motif2)";
            $parametres['motif1'] = '%' . $recherche . '%';
            $parametres['motif2'] = '%' . $recherche . '%';
        }
        if ($actifSeulement) {
            $sql .= " AND l.actif = 1";
        }
        $sql .= " ORDER BY l.numero, l.nom";
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

    public function recupererLigne($id) {
        $sql = "SELECT * FROM ligne_transport WHERE id_ligne = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $ligne = $query->fetch();
            return $ligne === false ? null : $ligne;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Compteurs du Back Office : total, lignes actives, tarif le plus bas et le plus élevé.
    public function statistiquesLignes() {
        $sql = "SELECT COUNT(*) AS total,
                       COALESCE(SUM(CASE WHEN actif = 1 THEN 1 ELSE 0 END), 0) AS actives,
                       COALESCE(MIN(tarif), 0) AS tarif_min,
                       COALESCE(MAX(tarif), 0) AS tarif_max
                FROM ligne_transport";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetch();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function numeroExiste($numero, $exclureId = null) {
        $sql = "SELECT COUNT(*) FROM ligne_transport WHERE numero = :numero AND id_ligne <> :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['numero' => $numero, 'id' => $exclureId ?? 0]);
            return (int) $query->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function ajouterLigne(LigneTransport $ligne) {
        $sql = "INSERT INTO ligne_transport (numero, nom, tarif, actif) VALUES (:numero, :nom, :tarif, :actif)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'numero' => $ligne->getNumero(),
                'nom' => $ligne->getNom(),
                'tarif' => $ligne->getTarif(),
                'actif' => $ligne->getActif(),
            ]);
            return (int) $db->lastInsertId();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function modifierLigne(LigneTransport $ligne, $id) {
        $sql = "UPDATE ligne_transport SET numero = :numero, nom = :nom, tarif = :tarif, actif = :actif WHERE id_ligne = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'numero' => $ligne->getNumero(),
                'nom' => $ligne->getNom(),
                'tarif' => $ligne->getTarif(),
                'actif' => $ligne->getActif(),
                'id' => $id,
            ]);
            return $query->rowCount() === 1 || $this->recupererLigne($id) !== null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Les stations et les horaires de la ligne sont supprimés par les clés étrangères (ON DELETE CASCADE).
    public function supprimerLigne($id) {
        $sql = "DELETE FROM ligne_transport WHERE id_ligne = :id";
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
            'numero' => posteText($post, 'numero'),
            'nom' => posteText($post, 'nom'),
            'tarif' => str_replace(',', '.', posteText($post, 'tarif')),
            'actif' => posteText($post, 'actif'),
        ];
    }

    public function verifierLigne(array $d, $id = null): array {
        $erreurs = [];
        if ($d['numero'] === '') {
            $erreurs['numero'] = 'Le numéro de la ligne est obligatoire.';
        } elseif (mb_strlen($d['numero']) > 20) {
            $erreurs['numero'] = 'Le numéro doit contenir au maximum 20 caractères.';
        } elseif ($this->numeroExiste($d['numero'], $id)) {
            $erreurs['numero'] = 'Une autre ligne utilise déjà ce numéro.';
        }
        if ($d['nom'] === '') {
            $erreurs['nom'] = 'Le nom de la ligne est obligatoire.';
        } elseif (mb_strlen($d['nom']) > 100) {
            $erreurs['nom'] = 'Le nom doit contenir au maximum 100 caractères.';
        }
        if (!preg_match('/^\d{1,3}(\.\d{1,2})?$/', $d['tarif'])) {
            $erreurs['tarif'] = 'Le tarif doit être un nombre entre 0 et 999.99, avec deux décimales au plus (ex : 0.80).';
        }
        if (!in_array($d['actif'], ['0', '1'], true)) {
            $erreurs['actif'] = 'Choisissez l’état de la ligne.';
        }
        return $erreurs;
    }

    private function nouvelleLigne(array $d, $id = null): LigneTransport {
        return new LigneTransport($id, $d['numero'], $d['nom'], round((float) $d['tarif'], 2), (int) $d['actif']);
    }

    // ==============================================================
    // BACK OFFICE
    // ==============================================================

    public function listBack(): void {
        CitoyenController::requireAdmin();

        $recherche = mb_substr(posteText($_GET, 'q'), 0, 100);
        $liste = $this->listerLignes($recherche);
        $stats = $this->statistiquesLignes();

        $this->renderBack('transport/ligneList', [
            'title' => 'E-Tounsi Admin - Lignes de transport',
            'currentPage' => 'admin-lignes',
            'section' => 'admin-lignes',
            'csrfToken' => csrfToken(),
            'liste' => $liste,
            'recherche' => $recherche,
            'stats' => $stats,
        ]);
    }

    public function addBack(): void {
        CitoyenController::requireAdmin();

        $erreurs = [];
        $old = ['numero' => '', 'nom' => '', 'tarif' => '', 'actif' => '1'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierLigne($old);
            }
            if (count($erreurs) == 0) {
                $this->ajouterLigne($this->nouvelleLigne($old));
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'La ligne a été ajoutée.'];
                header('Location: index.php?page=admin-lignes', true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $this->renderBack('transport/addLigne', [
            'title' => 'Ajouter une ligne',
            'currentPage' => 'admin-lignes',
            'section' => 'admin-lignes',
            'csrfToken' => csrfToken(),
            'old' => $old,
            'erreurs' => $erreurs,
        ]);
    }

    public function updateBack(): void {
        CitoyenController::requireAdmin();

        $id = posteId($_GET) ?? posteId($_POST);
        $ligne = $id === null ? null : $this->recupererLigne($id);
        if (!$ligne) {
            http_response_code(404);
            $this->renderBack('commun/problem', ['title' => 'Ligne introuvable', 'currentPage' => 'admin-lignes',
                'section' => 'admin-lignes', 'message' => 'Cet élément n’existe plus ou le lien est incorrect.']);
            return;
        }

        $erreurs = [];
        $old = [
            'numero' => $ligne['numero'],
            'nom' => $ligne['nom'],
            'tarif' => number_format((float) $ligne['tarif'], 2, '.', ''),
            'actif' => (string) (int) $ligne['actif'],
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierLigne($old, $id);
            }
            if (count($erreurs) == 0) {
                $this->modifierLigne($this->nouvelleLigne($old, $id), $id);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'La ligne a été mise à jour.'];
                header('Location: index.php?page=admin-lignes', true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $this->renderBack('transport/updateLigne', [
            'title' => 'Modifier une ligne',
            'currentPage' => 'admin-lignes',
            'section' => 'admin-lignes',
            'csrfToken' => csrfToken(),
            'id' => $id,
            'old' => $old,
            'erreurs' => $erreurs,
            'nbStations' => count((new StationController())->listerStations($id)),
            'nbHoraires' => count((new HoraireController())->listerHoraires($id)),
        ]);
    }

    public function deleteBack(): void {
        CitoyenController::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {
            $id = posteId($_POST);
            $supprime = $id !== null && $this->supprimerLigne($id);
            $_SESSION['flash'] = [
                'type' => $supprime ? 'success' : 'warning',
                'message' => $supprime ? 'La ligne, ses stations et ses horaires ont été supprimés.' : 'Cette ligne n’existe plus.',
            ];
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
        }

        header('Location: index.php?page=admin-lignes', true, 303);
        exit;
    }

    // ==============================================================
    // FRONT OFFICE : lignes actives, stations, horaires et prochain départ
    // ==============================================================

    public function listFront(): void {
        $recherche = mb_substr(posteText($_GET, 'q'), 0, 100);
        $stationC = new StationController();
        $horaireC = new HoraireController();

        $lignes = [];
        foreach ($this->listerLignes($recherche, true) as $ligne) {
            $horaires = $horaireC->listerHoraires((int) $ligne['id_ligne']);
            $lignes[] = [
                'ligne' => $ligne,
                'stations' => $stationC->listerStations((int) $ligne['id_ligne']),
                'horaires' => $horaires,
                'prochain' => HoraireController::prochainDepart($horaires),
            ];
        }

        $this->renderFront('transport/lignes', [
            'title' => tr('Lignes et horaires', 'الخطوط والمواقيت'),
            'currentPage' => 'lignes-transport',
            'recherche' => $recherche,
            'lignes' => $lignes,
        ]);
    }
}
