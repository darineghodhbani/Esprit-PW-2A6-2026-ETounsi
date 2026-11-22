<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Citoyen.php';

class CitoyenController extends Controller {
    public const CAPTCHA_SITE_KEY   = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    public const CAPTCHA_SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';


    private const LIBELLES_ROLES = [
        'citoyen' => 'Citoyen',
        'admin' => 'Administrateur',
    ];

    public function verifierCaptcha(): bool {
        if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify'
             . '?secret=' . self::CAPTCHA_SECRET_KEY
             . '&response=' . urlencode($_POST['g-recaptcha-response']);

        $contexte = stream_context_create(['http' => ['timeout' => 8]]);
        $reponse = @file_get_contents($url, false, $contexte);
        if ($reponse === false) return false;
        $resultat = json_decode($reponse, true);

        return is_array($resultat) && !empty($resultat['success']);
    }

    public function signup(): void {
        $erreurs = [];
        $old = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['nom']       = posteText($_POST, 'nom');
            $old['prenom']    = posteText($_POST, 'prenom');
            $old['email']     = posteText($_POST, 'email');
            $old['telephone'] = posteText($_POST, 'telephone');
            $mdp  = (isset($_POST['mot_de_passe']) && is_string($_POST['mot_de_passe'])) ? $_POST['mot_de_passe'] : '';
            $mdp2 = (isset($_POST['mot_de_passe2']) && is_string($_POST['mot_de_passe2'])) ? $_POST['mot_de_passe2'] : '';

            if (strlen($old['nom']) < 2 || strlen($old['nom']) > 50) {
                $erreurs['nom'] = 'Le nom doit contenir 2 à 50 caractères.';
            }
            if (strlen($old['prenom']) < 2 || strlen($old['prenom']) > 50) {
                $erreurs['prenom'] = 'Le prénom doit contenir 2 à 50 caractères.';
            }
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $erreurs['email'] = "L'email n'est pas valide.";
            }
            if ($old['telephone'] != '' && (strlen($old['telephone']) != 8 || !ctype_digit($old['telephone']))) {
                $erreurs['telephone'] = 'Le téléphone doit contenir 8 chiffres.';
            }
            if (strlen($mdp) < 8 || strlen($mdp) > 72) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir entre 8 caractères et 72 octets.';
            }
            if ($mdp != $mdp2) {
                $erreurs['mot_de_passe2'] = 'Les deux mots de passe ne correspondent pas.';
            }
            if (!$this->verifierCaptcha()) {
                $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            }

            if (count($erreurs) == 0) {
                $model = new Citoyen();
                $id = $model->create($old['nom'], $old['prenom'], $old['email'], $old['telephone'], $mdp);
                if ($id === false) {
                    $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
                } else {
                    header('Location: index.php?page=login&inscrit=1');
                    exit;
                }
            }
        }

        $title = 'Inscription — E-Tounsi';
        $currentPage = 'signup';
        require __DIR__ . '/../Views/frontoffice/signup.php';
    }

    public function login(): void {
        $erreurs = [];
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = posteText($_POST, 'email');
            $mdp   = (isset($_POST['mot_de_passe']) && is_string($_POST['mot_de_passe'])) ? $_POST['mot_de_passe'] : '';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs['email'] = "L'email n'est pas valide.";
            }
            if ($mdp == '') {
                $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
            }
            if (!$this->verifierCaptcha()) {
                $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            }

            if (count($erreurs) == 0) {
                $model   = new Citoyen();
                $citoyen = $model->verifyLogin($email, $mdp);

                if ($citoyen) {
                    connecterUtilisateur($citoyen);

                    if ($citoyen['role'] == 'citoyen') {
                        header('Location: index.php?page=home', true, 303);
                    } else {
                        header('Location: index.php?page=dashboard', true, 303);
                    }
                    exit;
                }
                $erreurs['global'] = 'Email ou mot de passe incorrect.';
            }
            $compteConcerne = filter_var($email, FILTER_VALIDATE_EMAIL) ? (new Citoyen())->findByEmail($email) : false;
            $raison = isset($erreurs['captcha']) ? 'CAPTCHA non validé.' : 'Identifiants non valides ou compte indisponible.';
            auditEvenement('LOGIN_FAILED', $raison, $compteConcerne ? $compteConcerne['id'] : null);

        }

        $title = 'Connexion — E-Tounsi';
        $currentPage = 'login';
        require __DIR__ . '/../Views/frontoffice/login.php';
    }

    public function logout(): void {
        deconnecterUtilisateur('Déconnexion demandée par l’utilisateur.');
        header('Location: index.php?page=home', true, 303);
        exit;
    }

    public function profile(): void {
        $citoyen = exigerConnexion();
        $erreurs = [];
        $valeurs = ['nom' => $citoyen['nom'], 'email' => $citoyen['email'], 'mot_de_passe_actuel' => '', 'password' => '', 'confirmation' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($valeurs as $champ => $valeur) {
                if (isset($_POST[$champ]) && is_string($_POST[$champ])) $valeurs[$champ] = $_POST[$champ];
            }
            $resultat = gestionUtilisateurs()->modifierProfil($citoyen['id'], $valeurs['nom'], $valeurs['email'],
                $valeurs['password'], $valeurs['confirmation'], $valeurs['mot_de_passe_actuel'], $citoyen['version_session']);
            $erreurs = $resultat['erreurs'];
            if ($erreurs === []) {
                connecterUtilisateur($resultat['utilisateur']);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre profil a été mis à jour.'];
                header('Location: index.php?page=profile', true, 303);
                exit;
            }
            http_response_code(422);
        }
        $this->renderFront('profile', ['title' => tr('Mon profil', 'ملفي الشخصي'), 'currentPage' => 'profile',
            'citoyen' => $citoyen, 'valeurs' => $valeurs, 'erreurs' => $erreurs]);
    }

    public static function requireLogin(): void {
        exigerConnexion();
    }

    public static function requireAdmin(): void {
        exigerRole(['admin']);
    }

    public function index()
    {
        exigerRole(['admin']);
        $recherche = posteText($_GET, 'q');
        $utilisateurs = [];
        foreach (gestionUtilisateurs()->lister() as $utilisateur) {
            if (posteMatches($recherche, $utilisateur['nom'] . ' ' . $utilisateur['email'])) {
                $utilisateurs[] = $utilisateur;
            }
        }

        posteRender('userList', 'Gestion des utilisateurs', 'utilisateurs', [
            'utilisateurs' => $utilisateurs,
            'recherche' => $recherche,
            'roles' => self::LIBELLES_ROLES,
        ]);
    }

    public function form()
    {
        exigerRole(['admin']);
        $utilisateur = ['id' => null, 'nom' => '', 'email' => '', 'role' => 'citoyen', 'actif' => 1];
        if (isset($_GET['id'])) {
            $utilisateur = $this->trouverPourFormulaire(posteId($_GET));
        }
        if ($utilisateur !== null) {
            $this->afficherFormulaire($utilisateur);
        }
    }

    public function save()
    {
        $compteConnecte = exigerRole(['admin']);
        if (!posteCsrfValid()) {
            return;
        }

        $id = null;
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            $id = posteId($_POST);
            if ($this->trouverPourFormulaire($id) === null) {
                return;
            }
        }

        $utilisateur = [
            'id' => $id,
            'nom' => posteText($_POST, 'nom'),
            'email' => posteText($_POST, 'email'),
            'role' => posteText($_POST, 'role'),
            'actif' => posteText($_POST, 'actif'),
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
        }

        $message = 'L’utilisateur a été ajouté.';
        if ($id !== null) {
            $message = 'L’utilisateur a été mis à jour.';
        }
        posteRedirect('utilisateurs', $message);
    }

    public function confirmDelete()
    {
        exigerRole(['admin']);
        $utilisateur = $this->trouverPourFormulaire(posteId($_GET));
        if ($utilisateur !== null) {
            $this->afficherSuppression($utilisateur);
        }
    }

    public function delete()
    {
        exigerRole(['admin']);
        if (!posteCsrfValid()) {
            return;
        }

        $id = posteId($_POST);
        $utilisateur = $this->trouverPourFormulaire($id);
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

        posteRedirect('utilisateurs', 'L’utilisateur a été supprimé.');
    }

    private function trouverPourFormulaire($id)
    {
        $utilisateur = null;
        if ($id !== null) {
            $utilisateur = gestionUtilisateurs()->trouver($id);
        }
        if ($utilisateur === null) {
            posteProblem(404, 'Utilisateur introuvable', 'Ce compte n’existe plus ou le lien est incorrect.', 'utilisateurs');
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

        posteRender('userForm', $titre, 'utilisateurs', [
            'utilisateur' => $utilisateur,
            'erreurs' => $erreurs,
            'roles' => self::LIBELLES_ROLES,
        ]);
    }

    private function afficherSuppression($utilisateur, $erreurs = [])
    {
        posteRender('supprimer', 'Supprimer l’utilisateur', 'utilisateurs', [
            'id' => $utilisateur['id'],
            'reference' => $utilisateur['nom'] . ' — ' . $utilisateur['email'],
            'action' => 'deleteUser',
            'message' => 'Le compte sera supprimé si aucune donnée métier ne lui est associée. Vous pouvez désactiver un compte pour conserver son historique.',
            'errors' => $erreurs,
        ]);
    }
}
