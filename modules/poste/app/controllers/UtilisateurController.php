<?php

class UtilisateurController extends Controller
{
    private const ROLES = [
        'citoyen' => 'Citoyen',
        'admin_poste' => 'Administrateur Poste',
        'super_admin' => 'Super administrateur',
    ];

    public function index()
    {
        exigerRole(['super_admin']);
        $recherche = $this->text($_GET, 'q');
        $utilisateurs = [];
        foreach (gestionUtilisateurs()->lister() as $utilisateur) {
            if ($this->matches($recherche, $utilisateur['nom'] . ' ' . $utilisateur['email'])) {
                $utilisateurs[] = $utilisateur;
            }
        }

        $this->render('utilisateurs', 'Gestion des utilisateurs', 'utilisateurs', [
            'utilisateurs' => $utilisateurs,
            'recherche' => $recherche,
            'roles' => self::ROLES,
        ]);
    }

    public function form()
    {
        exigerRole(['super_admin']);
        $utilisateur = ['id' => null, 'nom' => '', 'email' => '', 'role' => 'citoyen', 'actif' => 1];
        if (isset($_GET['id'])) {
            $utilisateur = $this->trouver($this->id($_GET));
        }
        if ($utilisateur !== null) {
            $this->afficherFormulaire($utilisateur);
        }
    }

    public function save()
    {
        $compteConnecte = exigerRole(['super_admin']);
        if (!$this->csrfValid()) {
            return;
        }

        $id = null;
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $id = $this->id($_POST);
            if ($this->trouver($id) === null) {
                return;
            }
        }

        $utilisateur = [
            'id' => $id,
            'nom' => $this->text($_POST, 'nom'),
            'email' => $this->text($_POST, 'email'),
            'role' => $this->text($_POST, 'role'),
            'actif' => $this->text($_POST, 'actif'),
        ];
        if (!in_array($utilisateur['actif'], ['0', '1'], true)) {
            $this->afficherFormulaire($utilisateur, ['actif' => 'Choisissez un état dans la liste.']);
            return;
        }

        // Le mot de passe est transmis sans supprimer les espaces saisis.
        $motDePasse = '';
        $confirmation = '';
        if (isset($_POST['password']) && is_string($_POST['password'])) {
            $motDePasse = $_POST['password'];
        }
        if (isset($_POST['confirmation']) && is_string($_POST['confirmation'])) {
            $confirmation = $_POST['confirmation'];
        }

        $versionAttendue = null;
        if ($id !== null && $id === (int) $compteConnecte['id']) {
            $versionAttendue = $compteConnecte['version_session'];
        }
        $resultat = gestionUtilisateurs()->enregistrer(
            $id,
            $utilisateur['nom'],
            $utilisateur['email'],
            $motDePasse,
            $confirmation,
            $utilisateur['role'],
            (int) $utilisateur['actif'],
            $versionAttendue
        );

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire($utilisateur, $resultat['erreurs']);
            return;
        }

        if ($id !== null && $id === (int) $compteConnecte['id']) {
            $compteModifie = $resultat['utilisateur'];
            if (!$compteModifie || !(int) $compteModifie['actif'] || $compteModifie['role'] === 'citoyen') {
                deconnecterUtilisateur();
                header('Location: ' . frontUrl('connexion'), true, 303);
                exit;
            }
            connecterUtilisateur($compteModifie);
            if ($compteModifie['role'] !== 'super_admin') {
                $this->redirect('dashboard', 'Votre compte a été mis à jour.');
            }
        }

        $message = 'L’utilisateur a été ajouté.';
        if ($id !== null) {
            $message = 'L’utilisateur a été mis à jour.';
        }
        $this->redirect('utilisateurs', $message);
    }

    public function confirmDelete()
    {
        exigerRole(['super_admin']);
        $utilisateur = $this->trouver($this->id($_GET));
        if ($utilisateur !== null) {
            $this->afficherSuppression($utilisateur);
        }
    }

    public function delete()
    {
        exigerRole(['super_admin']);
        if (!$this->csrfValid()) {
            return;
        }

        $id = $this->id($_POST);
        $utilisateur = $this->trouver($id);
        if ($utilisateur === null) {
            return;
        }

        $compteConnecte = utilisateurConnecte();
        $resultat = gestionUtilisateurs()->supprimer($id);
        if ($resultat['erreurs'] !== []) {
            http_response_code(409);
            $this->afficherSuppression($utilisateur, $resultat['erreurs']);
            return;
        }

        if ($id === (int) $compteConnecte['id']) {
            deconnecterUtilisateur();
            header('Location: ' . frontUrl('connexion'), true, 303);
            exit;
        }

        $this->redirect('utilisateurs', 'L’utilisateur a été supprimé.');
    }

    private function trouver($id)
    {
        $utilisateur = null;
        if ($id !== null) {
            $utilisateur = gestionUtilisateurs()->trouver($id);
        }
        if ($utilisateur === null) {
            $this->problem(404, 'Utilisateur introuvable', 'Ce compte n’existe plus ou le lien est incorrect.', 'utilisateurs');
        }
        return $utilisateur;
    }

    private function afficherFormulaire($utilisateur, $erreurs = [])
    {
        if ($erreurs !== []) {
            http_response_code(422);
        }

        $titre = 'Ajouter un utilisateur';
        if (!empty($utilisateur['id'])) {
            $titre = 'Modifier un utilisateur';
        }

        $this->render('utilisateur-form', $titre, 'utilisateurs', [
            'utilisateur' => $utilisateur,
            'erreurs' => $erreurs,
            'roles' => self::ROLES,
        ]);
    }

    private function afficherSuppression($utilisateur, $erreurs = [])
    {
        $this->render('suppression', 'Supprimer l’utilisateur', 'utilisateurs', [
            'id' => $utilisateur['id'],
            'reference' => $utilisateur['nom'] . ' — ' . $utilisateur['email'],
            'action' => 'utilisateur-delete',
            'message' => 'Le compte sera supprimé si aucune donnée métier ne lui est associée. Vous pouvez désactiver un compte pour conserver son historique.',
            'errors' => $erreurs,
        ]);
    }
}
