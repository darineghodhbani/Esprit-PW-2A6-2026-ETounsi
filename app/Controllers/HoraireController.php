<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Horaire.php';
require_once __DIR__ . '/LigneTransportController.php';

class HoraireController extends Controller {

    // ==============================================================
    // REQUÊTES SQL
    // ==============================================================

    // Horaires avec le numéro et le nom de leur ligne. $idLigne = 0 : toutes les lignes.
    public function listerHoraires($idLigne = 0) {
        $sql = "SELECT h.*, l.numero, l.nom AS ligne_nom
                FROM horaire h
                INNER JOIN ligne_transport l ON l.id_ligne = h.id_ligne
                WHERE 1 = 1";
        $parametres = [];
        if ((int) $idLigne > 0) {
            $sql .= " AND h.id_ligne = :id_ligne";
            $parametres['id_ligne'] = (int) $idLigne;
        }
        $sql .= " ORDER BY l.numero, h.heure_depart";
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

    public function recupererHoraire($id) {
        $sql = "SELECT * FROM horaire WHERE id_horaire = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $horaire = $query->fetch();
            return $horaire === false ? null : $horaire;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function compterParSens() {
        $sql = "SELECT COUNT(*) AS total,
                       COALESCE(SUM(CASE WHEN sens = 'Aller' THEN 1 ELSE 0 END), 0) AS aller,
                       COALESCE(SUM(CASE WHEN sens = 'Retour' THEN 1 ELSE 0 END), 0) AS retour
                FROM horaire";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetch();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function ajouterHoraire(Horaire $horaire) {
        $sql = "INSERT INTO horaire (id_ligne, sens, heure_depart, heure_arrivee, jours)
                VALUES (:id_ligne, :sens, :heure_depart, :heure_arrivee, :jours)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_ligne' => $horaire->getIdLigne(),
                'sens' => $horaire->getSens(),
                'heure_depart' => $horaire->getHeureDepart(),
                'heure_arrivee' => $horaire->getHeureArrivee(),
                'jours' => $horaire->getJours(),
            ]);
            return (int) $db->lastInsertId();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function modifierHoraire(Horaire $horaire, $id) {
        $sql = "UPDATE horaire SET id_ligne = :id_ligne, sens = :sens, heure_depart = :heure_depart,
                       heure_arrivee = :heure_arrivee, jours = :jours
                WHERE id_horaire = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_ligne' => $horaire->getIdLigne(),
                'sens' => $horaire->getSens(),
                'heure_depart' => $horaire->getHeureDepart(),
                'heure_arrivee' => $horaire->getHeureArrivee(),
                'jours' => $horaire->getJours(),
                'id' => $id,
            ]);
            return $query->rowCount() === 1 || $this->recupererHoraire($id) !== null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function supprimerHoraire($id) {
        $sql = "DELETE FROM horaire WHERE id_horaire = :id";
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
    // OUTILS : format des heures et prochain départ
    // ==============================================================

    // MySQL renvoie les colonnes TIME au format HH:MM:SS ; l'affichage utilise HH:MM.
    public static function heure($valeur): string {
        return substr((string) $valeur, 0, 5);
    }

    public static function valableLeJour(string $jours, int $jourSemaine): bool {
        if ($jours === 'Lundi - Vendredi') return $jourSemaine <= 5;
        if ($jours === 'Week-end') return $jourSemaine >= 6;
        return true;
    }

    // Premier départ restant aujourd'hui, à l'heure de Tunis (null s'il n'y en a plus).
    public static function prochainDepart(array $horaires, ?DateTimeImmutable $maintenant = null): ?array {
        $maintenant = $maintenant ?? new DateTimeImmutable('now', new DateTimeZone('Africa/Tunis'));
        $heure = $maintenant->format('H:i');
        $jour = (int) $maintenant->format('N');
        $prochain = null;
        foreach ($horaires as $horaire) {
            $depart = self::heure($horaire['heure_depart']);
            if (!self::valableLeJour($horaire['jours'], $jour) || $depart < $heure) continue;
            if ($prochain === null || $depart < self::heure($prochain['heure_depart'])) $prochain = $horaire;
        }
        return $prochain;
    }

    // ==============================================================
    // VALIDATION
    // ==============================================================

    public static function clean(array $post): array {
        $d = [
            'id_ligne' => posteText($post, 'id_ligne'),
            'sens' => posteText($post, 'sens'),
            'heure_depart' => posteText($post, 'heure_depart'),
            'heure_arrivee' => posteText($post, 'heure_arrivee'),
            'jours' => posteText($post, 'jours'),
        ];
        // Un navigateur peut envoyer HH:MM:SS : on garde HH:MM.
        foreach (['heure_depart', 'heure_arrivee'] as $champ) {
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $d[$champ])) $d[$champ] = substr($d[$champ], 0, 5);
        }
        return $d;
    }

    public function verifierHoraire(array $d): array {
        $erreurs = [];
        $formatHeure = '/^([01]\d|2[0-3]):[0-5]\d$/';
        if (!ctype_digit($d['id_ligne']) || (new LigneTransportController())->recupererLigne((int) $d['id_ligne']) === null) {
            $erreurs['id_ligne'] = 'Choisissez une ligne existante.';
        }
        if (!in_array($d['sens'], Horaire::SENS, true)) {
            $erreurs['sens'] = 'Choisissez le sens du trajet.';
        }
        if (!preg_match($formatHeure, $d['heure_depart'])) {
            $erreurs['heure_depart'] = 'Format attendu : HH:MM (exemple : 07:30).';
        }
        if (!preg_match($formatHeure, $d['heure_arrivee'])) {
            $erreurs['heure_arrivee'] = 'Format attendu : HH:MM (exemple : 08:10).';
        }
        if (!isset($erreurs['heure_depart']) && !isset($erreurs['heure_arrivee']) && $d['heure_arrivee'] <= $d['heure_depart']) {
            $erreurs['heure_arrivee'] = 'L’heure d’arrivée doit être après l’heure de départ.';
        }
        if (!in_array($d['jours'], Horaire::JOURS, true)) {
            $erreurs['jours'] = 'Choisissez les jours de circulation.';
        }
        return $erreurs;
    }

    private function nouvelHoraire(array $d, $id = null): Horaire {
        return new Horaire($id, (int) $d['id_ligne'], $d['sens'], $d['heure_depart'] . ':00', $d['heure_arrivee'] . ':00', $d['jours']);
    }

    private static function retour($idLigne): string {
        return 'index.php?page=admin-horaires' . ((int) $idLigne > 0 ? '&ligne=' . (int) $idLigne : '');
    }

    // ==============================================================
    // BACK OFFICE
    // ==============================================================

    public function listBack(): void {
        CitoyenController::requireAdmin();

        $filtre = (int) posteText($_GET, 'ligne');
        $liste = $this->listerHoraires($filtre);
        $jour = (int) (new DateTimeImmutable('now', new DateTimeZone('Africa/Tunis')))->format('N');
        $nbAujourdhui = 0;
        foreach ($this->listerHoraires() as $horaire) {
            if (self::valableLeJour($horaire['jours'], $jour)) $nbAujourdhui++;
        }

        $this->renderBack('transport/horaireList', [
            'title' => 'E-Tounsi Admin - Horaires',
            'currentPage' => 'admin-horaires',
            'section' => 'admin-horaires',
            'csrfToken' => csrfToken(),
            'liste' => $liste,
            'lignes' => (new LigneTransportController())->listerLignes(),
            'filtre' => $filtre,
            'stats' => $this->compterParSens(),
            'nbAujourdhui' => $nbAujourdhui,
        ]);
    }

    public function addBack(): void {
        CitoyenController::requireAdmin();

        $erreurs = [];
        $old = ['id_ligne' => (string) (int) posteText($_GET, 'ligne'), 'sens' => '', 'heure_depart' => '', 'heure_arrivee' => '', 'jours' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierHoraire($old);
            }
            if (count($erreurs) == 0) {
                $this->ajouterHoraire($this->nouvelHoraire($old));
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’horaire a été ajouté.'];
                header('Location: ' . self::retour($old['id_ligne']), true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $this->renderBack('transport/addHoraire', [
            'title' => 'Ajouter un horaire',
            'currentPage' => 'admin-horaires',
            'section' => 'admin-horaires',
            'csrfToken' => csrfToken(),
            'old' => $old,
            'erreurs' => $erreurs,
            'lignes' => (new LigneTransportController())->listerLignes(),
        ]);
    }

    public function updateBack(): void {
        CitoyenController::requireAdmin();

        $id = posteId($_GET) ?? posteId($_POST);
        $horaire = $id === null ? null : $this->recupererHoraire($id);
        if (!$horaire) {
            http_response_code(404);
            $this->renderBack('commun/problem', ['title' => 'Horaire introuvable', 'currentPage' => 'admin-horaires',
                'section' => 'admin-horaires', 'message' => 'Cet élément n’existe plus ou le lien est incorrect.']);
            return;
        }

        $erreurs = [];
        $old = [
            'id_ligne' => (string) $horaire['id_ligne'],
            'sens' => $horaire['sens'],
            'heure_depart' => self::heure($horaire['heure_depart']),
            'heure_arrivee' => self::heure($horaire['heure_arrivee']),
            'jours' => $horaire['jours'],
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierHoraire($old);
            }
            if (count($erreurs) == 0) {
                $this->modifierHoraire($this->nouvelHoraire($old, $id), $id);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’horaire a été mis à jour.'];
                header('Location: ' . self::retour($old['id_ligne']), true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $this->renderBack('transport/updateHoraire', [
            'title' => 'Modifier un horaire',
            'currentPage' => 'admin-horaires',
            'section' => 'admin-horaires',
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
            $supprime = $id !== null && $this->supprimerHoraire($id);
            $_SESSION['flash'] = [
                'type' => $supprime ? 'success' : 'warning',
                'message' => $supprime ? 'L’horaire a été supprimé.' : 'Cet horaire n’existe plus.',
            ];
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
        }

        header('Location: ' . self::retour($retour), true, 303);
        exit;
    }
}
