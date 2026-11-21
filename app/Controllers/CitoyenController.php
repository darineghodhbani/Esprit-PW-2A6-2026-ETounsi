<?php

require_once __DIR__ . '/../Models/Citoyen.php';

class CitoyenController {

    public function signup(): void {
        $erreurs = [];
        $old = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['nom']       = trim($_POST['nom']);
            $old['prenom']    = trim($_POST['prenom']);
            $old['email']     = trim($_POST['email']);
            $old['telephone'] = trim($_POST['telephone']);
            $mdp  = $_POST['mot_de_passe'];
            $mdp2 = $_POST['mot_de_passe2'];

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
            if (strlen($mdp) < 6) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            if ($mdp != $mdp2) {
                $erreurs['mot_de_passe2'] = 'Les deux mots de passe ne correspondent pas.';
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
            $email = trim($_POST['email']);
            $mdp   = $_POST['mot_de_passe'];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs['email'] = "L'email n'est pas valide.";
            }
            if ($mdp == '') {
                $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
            }

            if (count($erreurs) == 0) {
                $model   = new Citoyen();
                $citoyen = $model->verifyLogin($email, $mdp);

                if ($citoyen) {
                    $_SESSION['id_citoyen'] = $citoyen['id_citoyen'];
                    $_SESSION['nom']        = $citoyen['nom'];
                    $_SESSION['prenom']     = $citoyen['prenom'];
                    $_SESSION['role']       = $citoyen['role'];

                    if ($citoyen['role'] == 'citoyen') {
                        header('Location: index.php?page=home');
                    } else {
                        header('Location: index.php?page=dashboard');
                    }
                    exit;
                }
                $erreurs['global'] = 'Email ou mot de passe incorrect.';
            }
        }

        $title = 'Connexion — E-Tounsi';
        $currentPage = 'login';
        require __DIR__ . '/../Views/frontoffice/login.php';
    }

    public function logout(): void {
        session_unset();
        session_destroy();
        header('Location: index.php?page=home');
        exit;
    }

    public function profile(): void {
        self::requireLogin();

        $model = new Citoyen();
        $citoyen = $model->findById($_SESSION['id_citoyen']);

        $title = 'Mon profil — E-Tounsi';
        $currentPage = 'profile';
        require __DIR__ . '/../Views/frontoffice/profile.php';
    }

    public static function requireLogin(): void {
        if (!isset($_SESSION['id_citoyen'])) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    public static function requireAdmin(): void {
        self::requireLogin();
        if ($_SESSION['role'] != 'agent' && $_SESSION['role'] != 'admin') {
            http_response_code(403);
            die('Accès refusé : vous n\'avez pas les droits pour cette page.');
        }
    }
}
