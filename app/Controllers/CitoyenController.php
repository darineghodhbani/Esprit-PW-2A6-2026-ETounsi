<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Models/Citoyen.php';

class CitoyenController extends Controller {
    private $modele;
    public const ROLES = ['citoyen' => 'Citoyen', 'admin' => 'Administrateur'];
    public const CAPTCHA_SITE_KEY = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    public const CAPTCHA_SECRET_KEY = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

    public function __construct() {
        $this->modele = new Citoyen();
    }

    public function listerCitoyens() {
        return $this->modele->listerCitoyens();
    }

    public function recupererCitoyen($id) {
        return $this->modele->recupererCitoyen($id);
    }

    public function ajouterCitoyen(Citoyen $citoyen, $confirmation) {
        return $this->modele->ajouterCitoyen($citoyen, $confirmation);
    }

    public function modifierCitoyen(Citoyen $citoyen, $id, $confirmation) {
        return $this->modele->modifierCitoyen($citoyen, $id, $confirmation);
    }

    public function supprimerCitoyen($id) {
        return $this->modele->supprimerCitoyen($id);
    }

    public static function clean(array $post): array {
        $password = $post['password'] ?? $post['mot_de_passe'] ?? '';
        $confirmation = $post['confirmation'] ?? $post['mot_de_passe2'] ?? '';
        return [
            'nom' => posteText($post, 'nom'), 'prenom' => posteText($post, 'prenom'),
            'email' => posteText($post, 'email'), 'telephone' => posteText($post, 'telephone'),
            'role' => posteText($post, 'role'), 'actif' => posteText($post, 'actif'),
            'password' => is_string($password) ? $password : '',
            'confirmation' => is_string($confirmation) ? $confirmation : '',
        ];
    }

    public function verifierCitoyen(array $d, bool $creation = true, bool $inscription = false): array {
        $erreurs = [];
        $limiteNom = $inscription ? 50 : 80;
        if (mb_strlen($d['nom']) < 2 || mb_strlen($d['nom']) > $limiteNom || preg_match('/[<>\x00-\x1F]/u', $d['nom'])) {
            $erreurs['nom'] = 'Saisissez un nom de 2 à ' . $limiteNom . ' caractères, sans balises ni caractères de contrôle.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || strlen($d['email']) > 150) $erreurs['email'] = 'Saisissez une adresse email valide.';
        if (!isset(self::ROLES[$d['role']])) $erreurs['role'] = 'Choisissez un rôle proposé dans la liste.';
        if (!in_array($d['actif'], ['0', '1'], true)) $erreurs['actif'] = 'Choisissez un état de compte valide.';
        $champPassword = $inscription ? 'mot_de_passe' : 'password';
        $champConfirmation = $inscription ? 'mot_de_passe2' : 'confirmation';
        if ($creation || $d['password'] !== '') {
            if (mb_strlen($d['password']) < 8 || strlen($d['password']) > 72 || strpos($d['password'], "\0") !== false) {
                $erreurs[$champPassword] = 'Le mot de passe doit contenir au moins 8 caractères et au maximum 72 octets.';
            }
        }
        if ($d['password'] !== $d['confirmation']) $erreurs[$champConfirmation] = 'Les mots de passe doivent être identiques.';
        if ($inscription) {
            if (mb_strlen($d['prenom']) < 2 || mb_strlen($d['prenom']) > 50) $erreurs['prenom'] = 'Le prénom doit contenir 2 à 50 caractères.';
            if ($d['telephone'] !== '' && (strlen($d['telephone']) !== 8 || !ctype_digit($d['telephone']))) $erreurs['telephone'] = 'Le téléphone doit contenir 8 chiffres.';
        }
        return $erreurs;
    }

    public function verifierCaptcha(): bool {
        $token = posteText($_POST, 'g-recaptcha-response');
        if ($token === '') return false;
        $url = 'https://www.google.com/recaptcha/api/siteverify'
             . '?secret=' . self::CAPTCHA_SECRET_KEY . '&response=' . urlencode($token);
        $contexte = stream_context_create(['http' => ['timeout' => 8]]);
        $reponse = @file_get_contents($url, false, $contexte);
        if ($reponse === false) return false;
        $resultat = json_decode($reponse, true);
        return is_array($resultat) && !empty($resultat['success']);
    }

    public function signup(): void {
        $erreurs = [];
        $old = self::clean([]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['role'] = 'citoyen';
            $old['actif'] = '1';
            $erreurs = $this->verifierCitoyen($old, true, true);
            if (!$this->verifierCaptcha()) $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            if (count($erreurs) == 0) {
                $citoyen = new Citoyen(null, $old['nom'], $old['email'], $old['password'], 'citoyen', 1, $old['prenom'], $old['telephone']);
                $resultat = $this->ajouterCitoyen($citoyen, $old['confirmation']);
                $erreurs = $resultat['erreurs'];
                if (count($erreurs) == 0) {
                    header('Location: index.php?page=login&inscrit=1', true, 303);
                    exit;
                }
            }
        }
        $this->renderFront('signup', ['title' => 'Inscription — E-Tounsi', 'currentPage' => 'signup', 'old' => $old, 'erreurs' => $erreurs]);
    }

    public function login(): void {
        $erreurs = [];
        $old = ['email' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $erreurs['email'] = 'L’email n’est pas valide.';
            if ($old['password'] === '') $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
            if (!$this->verifierCaptcha()) $erreurs['captcha'] = 'Veuillez cocher la case « Je ne suis pas un robot ».';
            if (count($erreurs) == 0) {
                $citoyen = $this->modele->verifyLogin($old['email'], $old['password']);
                if ($citoyen) {
                    connecterUtilisateur($citoyen);
                    header('Location: index.php?page=' . ($citoyen['role'] === 'citoyen' ? 'home' : 'dashboard'), true, 303);
                    exit;
                }
                $erreurs['global'] = 'Email ou mot de passe incorrect.';
            }
            $compte = filter_var($old['email'], FILTER_VALIDATE_EMAIL) ? $this->modele->findByEmail($old['email']) : false;
            $raison = isset($erreurs['captcha']) ? 'CAPTCHA non validé.' : 'Identifiants non valides ou compte indisponible.';
            auditEvenement('LOGIN_FAILED', $raison, $compte ? $compte['id'] : null);
        }
        $this->renderFront('login', ['title' => 'Connexion — E-Tounsi', 'currentPage' => 'login', 'old' => $old, 'erreurs' => $erreurs]);
    }

    public function logout(): void {
        deconnecterUtilisateur('Déconnexion demandée par l’utilisateur.');
        header('Location: index.php?page=home', true, 303);
        exit;
    }

    public function profile(): void {
        $citoyen = exigerConnexion();
        $erreurs = [];
        $old = ['nom' => $citoyen['nom'], 'email' => $citoyen['email'], 'mot_de_passe_actuel' => '', 'password' => '', 'confirmation' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $champ => $valeur) {
                if (isset($_POST[$champ]) && is_string($_POST[$champ])) $old[$champ] = $_POST[$champ];
            }
            $resultat = $this->modele->modifierProfil($citoyen['id'], $old['nom'], $old['email'],
                $old['password'], $old['confirmation'], $old['mot_de_passe_actuel'], $citoyen['version_session']);
            $erreurs = $resultat['erreurs'];
            if (count($erreurs) == 0) {
                connecterUtilisateur($resultat['utilisateur']);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre profil a été mis à jour.'];
                header('Location: index.php?page=profile', true, 303);
                exit;
            }
            http_response_code(422);
        }
        $this->renderFront('profile', ['title' => t('profil.titre'), 'currentPage' => 'profile', 'citoyen' => $citoyen, 'old' => $old, 'erreurs' => $erreurs]);
    }

    public static function requireLogin(): void {
        exigerConnexion();
    }

    public static function requireAdmin(): void {
        exigerRole(['admin']);
    }

    public function listBack(): void {
        self::requireAdmin();
        $recherche = posteText($_GET, 'q');
        $liste = [];
        foreach ($this->listerCitoyens() as $citoyen) {
            if (posteMatches($recherche, $citoyen['nom'] . ' ' . $citoyen['email'])) $liste[] = $citoyen;
        }
        $this->renderBack('utilisateurs/userList', [
            'title' => 'Gestion des utilisateurs', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
            'liste' => $liste, 'recherche' => $recherche, 'roles' => self::ROLES,
        ]);
    }

    public function addBack(): void {
        $this->renderBack('utilisateurs/addUser');
    }

    public function updateBack(): void {
        $compteConnecte = exigerRole(['admin']);
        $id = posteId($_GET) ?? posteId($_POST);
        $old = $id === null ? null : $this->recupererCitoyen($id);
        if (!$old) { $this->introuvable(); return; }
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = self::clean($_POST);
            $old['id'] = $id;
            $erreurs = $this->verifierCitoyen($old, false);
            if (count($erreurs) == 0) {
                $version = $id === $compteConnecte['id'] ? $compteConnecte['version_session'] : null;
                $citoyen = new Citoyen($id, $old['nom'], $old['email'], $old['password'], $old['role'], (int) $old['actif'], null, null, $version);
                $resultat = $this->modifierCitoyen($citoyen, $id, $old['confirmation']);
                $erreurs = $resultat['erreurs'];
                if (count($erreurs) == 0) {
                    if ($id === $compteConnecte['id']) {
                        $compte = $resultat['utilisateur'];
                        if (!$compte || $compte['actif'] !== 1 || $compte['role'] !== 'admin') {
                            deconnecterUtilisateur();
                            header('Location: ' . frontUrl('connexion'), true, 303);
                            exit;
                        }
                        connecterUtilisateur($compte);
                    }
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été mis à jour.'];
                    header('Location: index.php?page=admin-utilisateurs', true, 303);
                    exit;
                }
            }
            http_response_code(422);
        }
        $this->renderBack('utilisateurs/updateUser', [
            'title' => 'Modifier un utilisateur', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
            'old' => $old, 'erreurs' => $erreurs, 'roles' => self::ROLES,
        ]);
    }

    public function deleteBack(): void {
        self::requireAdmin();
        $id = $_SERVER['REQUEST_METHOD'] === 'POST' ? posteId($_POST) : posteId($_GET);
        $citoyen = $id === null ? null : $this->recupererCitoyen($id);
        if (!$citoyen) { $this->introuvable(); return; }
        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultat = $this->supprimerCitoyen($id);
            $erreurs = $resultat['erreurs'];
            if (count($erreurs) == 0) {
                if ($id === (int) $_SESSION['user_id']) {
                    deconnecterUtilisateur();
                    header('Location: ' . frontUrl('connexion'), true, 303);
                    exit;
                }
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été supprimé.'];
                header('Location: index.php?page=admin-utilisateurs', true, 303);
                exit;
            }
            http_response_code(409);
        }
        $this->renderBack('commun/supprimer', [
            'title' => 'Supprimer l’utilisateur', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs', 'erreurs' => $erreurs,
            'old' => ['id' => $id, 'reference' => $citoyen['nom'] . ' — ' . $citoyen['email'], 'action' => 'deleteUser',
                'message' => 'Le compte sera supprimé si aucune donnée métier ne lui est associée. Vous pouvez le désactiver pour conserver son historique.'],
        ]);
    }

    private function introuvable(): void {
        http_response_code(404);
        $this->renderBack('commun/problem', [
            'title' => 'Utilisateur introuvable', 'currentPage' => 'admin-utilisateurs', 'section' => 'utilisateurs',
            'message' => 'Ce compte n’existe plus ou le lien est incorrect.',
        ]);
    }
}
