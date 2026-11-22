<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/BureauPostal.php';

class BureauPostalController extends Controller {
    public function listerBureaux() {
        $sql = "SELECT * FROM bureau_postal ORDER BY ville, nom";
        $db = Database::getConnection();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function recupererBureau($id) {
        $sql = "SELECT * FROM bureau_postal WHERE id_bureau = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $bureau = $query->fetch();
            return $bureau === false ? null : $bureau;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function compterBureaux() {
        $sql = "SELECT COUNT(*) FROM bureau_postal";
        $db = Database::getConnection();
        try {
            return (int) $db->query($sql)->fetchColumn();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function compterVilles() {
        $sql = "SELECT COUNT(DISTINCT ville) FROM bureau_postal";
        $db = Database::getConnection();
        try {
            return (int) $db->query($sql)->fetchColumn();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function ajouterBureau(BureauPostal $bureau) {
        $sql = "INSERT INTO bureau_postal (nom, adresse, ville) VALUES (:nom, :adresse, :ville)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $bureau->getNom(),
                'adresse' => $bureau->getAdresse(),
                'ville' => $bureau->getVille(),
            ]);
            return (int) $db->lastInsertId();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function modifierBureau(BureauPostal $bureau, $id) {
        $sql = "UPDATE bureau_postal SET nom = :nom, adresse = :adresse, ville = :ville WHERE id_bureau = :id";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $bureau->getNom(),
                'adresse' => $bureau->getAdresse(),
                'ville' => $bureau->getVille(),
                'id' => $id,
            ]);
            return $query->rowCount() === 1 || $this->recupererBureau($id) !== null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function supprimerBureau($id) {
        $sql = "DELETE FROM bureau_postal WHERE id_bureau = :id
                AND id_bureau NOT IN (SELECT id_bureau_depart FROM colis)";
        $db = Database::getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount() === 1;
        } catch (PDOException $e) {
            if (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1451) return false;
            throw $e;
        }
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
        CitoyenController::requireAdmin();

        $colisC = new ColisController();
        $liste = $this->listerBureaux();
        $nbBureaux = $this->compterBureaux();
        $nbVilles = $this->compterVilles();
        $nbColis = $colisC->compterColis();

        $title = 'E-Tounsi Admin - Bureaux de poste';
        $currentPage = 'admin-bureaux';
        $section = 'bureaux';
        $csrfToken = csrfToken();

        $this->renderBack('poste/bureauList', [
            'title' => $title,
            'currentPage' => $currentPage,
            'section' => $section,
            'csrfToken' => $csrfToken,
            'liste' => $liste,
            'nbBureaux' => $nbBureaux,
            'nbVilles' => $nbVilles,
            'nbColis' => $nbColis,
        ]);
    }

    public function addBack(): void {
        CitoyenController::requireAdmin();

        $erreurs = [];
        $old = ['nom' => '', 'adresse' => '', 'ville' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierBureau($old);
            }
            if (count($erreurs) == 0) {
                $bureau = new BureauPostal(null, $old['nom'], $old['adresse'], $old['ville']);
                $this->ajouterBureau($bureau);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le bureau a été ajouté.'];
                header('Location: index.php?page=admin-bureaux', true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $title = 'Ajouter un bureau';
        $currentPage = 'admin-bureaux';
        $section = 'bureaux';
        $csrfToken = csrfToken();

        $this->renderBack('poste/addBureau', [
            'title' => $title,
            'currentPage' => $currentPage,
            'section' => $section,
            'csrfToken' => $csrfToken,
            'old' => $old,
            'erreurs' => $erreurs,
        ]);
    }

    public function updateBack(): void {
        CitoyenController::requireAdmin();

        $title = 'Modifier un bureau';
        $currentPage = 'admin-bureaux';
        $section = 'bureaux';

        $id = posteId($_GET) ?? posteId($_POST);
        $old = $id === null ? null : $this->recupererBureau($id);
        if (!$old) {
            http_response_code(404);
            $title = 'Bureau introuvable';
            $message = 'Cet élément n’existe plus ou le lien est incorrect.';
            $this->renderBack('commun/problem', ['title' => $title, 'currentPage' => $currentPage, 'section' => $section, 'message' => $message]);
            return;
        }
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id_bureau'] = $id;
            if (!csrfValide($_POST['csrf'] ?? '')) {
                $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $erreurs = $this->verifierBureau($old);
            }
            if (count($erreurs) == 0) {
                $bureau = new BureauPostal($id, $old['nom'], $old['adresse'], $old['ville']);
                if (!$this->modifierBureau($bureau, $id)) {
                    http_response_code(404);
                    $title = 'Bureau introuvable';
                    $message = 'Cet élément n’existe plus ou le lien est incorrect.';
                    $this->renderBack('commun/problem', ['title' => $title, 'currentPage' => $currentPage, 'section' => $section, 'message' => $message]);
                    return;
                }
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le bureau a été mis à jour.'];
                header('Location: index.php?page=admin-bureaux', true, 303);
                exit;
            }
            http_response_code(isset($erreurs['global']) ? 403 : 422);
        }

        $csrfToken = csrfToken();

        $this->renderBack('poste/updateBureau', [
            'title' => $title,
            'currentPage' => $currentPage,
            'section' => $section,
            'csrfToken' => $csrfToken,
            'id' => $id,
            'old' => $old,
            'erreurs' => $erreurs,
        ]);
    }

    public function deleteBack(): void {
        CitoyenController::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValide($_POST['csrf'] ?? '')) {

            $id = posteId($_POST);
            $supprime = $id !== null && $this->supprimerBureau($id);
            $_SESSION['flash'] = [
                'type' => $supprime ? 'success' : 'warning',
                'message' => $supprime ? 'Le bureau a été supprimé.' : 'Bureau introuvable ou associé à des colis : suppression refusée.',
            ];
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') auditEvenement('ACCESS_DENIED', 'Jeton CSRF invalide pour la page ' . pageAudit() . '.');
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action refusée (jeton de sécurité invalide).'];
        }

        header('Location: index.php?page=admin-bureaux', true, 303);
        exit;
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

}
