<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/BureauPostal.php';

class BureauPostalController extends Controller {
    public function listerBureaux() {
        $sql = "SELECT * FROM bureau_postal ORDER BY ville, nom";
        $db = Database::getConnection();
        return $db->query($sql)->fetchAll();
    }

    public function recupererBureau($id) {
        $sql = "SELECT * FROM bureau_postal WHERE id_bureau = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        $bureau = $query->fetch();
        return $bureau === false ? null : $bureau;
    }

    public function compterBureaux() {
        $sql = "SELECT COUNT(*) FROM bureau_postal";
        $db = Database::getConnection();
        return (int) $db->query($sql)->fetchColumn();
    }

    public function compterVilles() {
        $sql = "SELECT COUNT(DISTINCT ville) FROM bureau_postal";
        $db = Database::getConnection();
        return (int) $db->query($sql)->fetchColumn();
    }

    public function ajouterBureau(BureauPostal $bureau) {
        $sql = "INSERT INTO bureau_postal (nom, adresse, ville) VALUES (:nom, :adresse, :ville)";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute([
            'nom' => $bureau->getNom(),
            'adresse' => $bureau->getAdresse(),
            'ville' => $bureau->getVille(),
        ]);
        return (int) $db->lastInsertId();
    }

    public function modifierBureau(BureauPostal $bureau, $id) {
        $sql = "UPDATE bureau_postal SET nom = :nom, adresse = :adresse, ville = :ville WHERE id_bureau = :id";
        $db = Database::getConnection();
        $query = $db->prepare($sql);
        $query->execute([
            'nom' => $bureau->getNom(),
            'adresse' => $bureau->getAdresse(),
            'ville' => $bureau->getVille(),
            'id' => $id,
        ]);
        return $query->rowCount() === 1 || $this->recupererBureau($id) !== null;
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
        $this->renderBack('poste/bureauList');
    }

    public function addBack(): void {
        $this->renderBack('poste/addBureau');
    }

    public function updateBack(): void {
        $this->renderBack('poste/updateBureau');
    }

    public function deleteBack(): void {
        require __DIR__ . '/../Views/backoffice/pages/poste/deleteBureau.php';
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
