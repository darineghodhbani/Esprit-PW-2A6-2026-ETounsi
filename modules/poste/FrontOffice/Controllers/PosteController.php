<?php

class PosteController
{
    private $modele;

    public function __construct($modele)
    {
        $this->modele = $modele;
    }

    public function chercherColis($numeroSuivi)
    {
        return $this->modele->track($numeroSuivi);
    }

    public function listerBureaux()
    {
        return $this->modele->listOffices();
    }

    public function filtrerBureaux($ville)
    {
        $bureaux = $this->listerBureaux();
        $villes = [];
        foreach ($bureaux as $bureau) {
            if (!in_array($bureau['ville'], $villes, true)) {
                $villes[] = $bureau['ville'];
            }
        }
        natcasesort($villes);

        if (!is_string($ville) || !in_array($ville, $villes, true)) {
            $ville = '';
        }

        $resultats = [];
        foreach ($bureaux as $bureau) {
            if ($ville === '' || $bureau['ville'] === $ville) {
                $resultats[] = $bureau;
            }
        }

        return ['bureaux' => $resultats, 'villes' => $villes, 'ville' => $ville];
    }

    public function declarerEnvoi($idRecu)
    {
        exigerRole(['citoyen']);
        $utilisateur = utilisateurConnecte();
        $idBureau = $this->identifiantValide($idRecu);
        if ($idBureau === null || !$this->modele->findOffice($idBureau)) {
            http_response_code(422);
            return tr('Choisissez un bureau de départ dans la liste.', 'اختر مكتب الإرسال من القائمة.');
        }

        try {
            $colis = $this->modele->createParcel($idBureau, $utilisateur['id']);
        } catch (PDOException $erreur) {
            if ($this->modele->findOffice($idBureau) !== null) {
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
        return $this->modele->listParcels($utilisateur['id']);
    }

    public function annulerEnvoi($idRecu)
    {
        exigerRole(['citoyen']);
        $utilisateur = utilisateurConnecte();
        $idColis = $this->identifiantValide($idRecu);
        if ($idColis !== null && $this->modele->cancelParcel($idColis, $utilisateur['id'])) {
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
}
