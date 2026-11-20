<?php

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/ProprieteController.php';
require_once __DIR__ . '/ImpotController.php';

class HomeController
{
    private AuthController $auth;

    public function __construct(AuthController $auth)
    {
        $this->auth = $auth;
    }

    private function render($view, $pageTitle, $currentPage, array $data = [])
    {
        extract($data);
        $csrfToken = AuthController::getCsrfToken();
        $flash = AuthController::getFlash();
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public function notFound()    { http_response_code(404); $this->render('404', 'E-Tounsi - Page introuvable', '404'); }

    public function home()
    {
        $services = [
            ['nom' => 'Transport',            'icone' => 'fa-bus',        'image' => 'transport-office.jpg',    'lien' => '#services'],
            ['nom' => 'Municipalité',         'icone' => 'fa-building',   'image' => 'municipalite-office.jpg', 'lien' => '#services'],
            ['nom' => 'CNAM',                 'icone' => 'fa-heartbeat',  'image' => 'cnam-office.jpg',         'lien' => '#services'],
            ['nom' => 'Poste',                'icone' => 'fa-envelope',   'image' => 'bureau-poste.jpg',        'lien' => '#services'],
            ['nom' => 'Recette des Finances', 'icone' => 'fa-landmark',   'image' => 'finances-office.jpg',     'lien' => 'index.php?page=administratif&porte=1'],
        ];
        $this->render('home', 'E-Tounsi - Vos services publics, en toute simplicité', 'home', compact('services'));
    }

    public function administratif()
    {
        AuthController::requireLogin();
        $proprietes = new ProprieteController();
        $impots = new ImpotController();
        $idUser = (int) $_SESSION['user_id'];

        $errors = [];
        $old = ['type' => '', 'reference' => '', 'adresse' => '', 'valeur' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                AuthController::flash('danger', 'Action refusée (jeton de sécurité invalide).');
                $this->redirect('index.php?page=administratif');
            }

            if (($_POST['form'] ?? '') === 'payer') {
                if ($impots->demanderPaiement((int) ($_POST['id'] ?? 0), $idUser)) {
                    AuthController::flash('success', 'Demande enregistrée. Rendez-vous au bureau de poste pour régler le montant. '
                        . "Votre impôt sera marqué « Payé » après validation par l'administration.");
                } else {
                    AuthController::flash('danger', 'Paiement impossible.');
                }
                $this->redirect('index.php?page=administratif');
            }

            $old = ProprieteController::clean($_POST);
            $old['id_user'] = $idUser;
            $errors = $proprietes->validate($old);

            if (!$errors) {
                $p = new Propriete();
                $p->setIdUser($idUser);
                $p->setType($old['type']);
                $p->setReference($old['reference']);
                $p->setAdresse($old['adresse']);
                $p->setValeur((int) $old['valeur']);
                $proprietes->create($p);
                AuthController::flash('success', 'Propriété ajoutée avec succès.');
                $this->redirect('index.php?page=administratif');
            }
        }

        $mesProprietes = $proprietes->findByUser($idUser);
        $mesImpots = $impots->findByUser($idUser);
        $this->render('administratif', 'E-Tounsi - Recette des Finances', 'administratif',
            compact('errors', 'old', 'mesProprietes', 'mesImpots'));
    }

    public function login()
    {
        if (AuthController::isAuthenticated()) {
            $this->redirect(AuthController::hasRole('admin') ? 'admin.php' : 'index.php?page=profil');
        }

        $errors = [];
        $old = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$errors, $old] = $this->auth->login($_POST);
            if (!$errors) {
                AuthController::flash('success', 'Bienvenue ' . $_SESSION['prenom'] . ' !');

                $this->redirect(AuthController::hasRole('admin') ? 'admin.php' : 'index.php?page=profil');
            }
        }
        $this->render('login', 'E-Tounsi - Connexion', 'login', compact('errors', 'old'));
    }

    public function register()
    {
        if (AuthController::isAuthenticated()) {
            $this->redirect('index.php?page=profil');
        }

        $errors = [];
        $old = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$errors, $old] = $this->auth->register($_POST);
            if (!$errors) {
                AuthController::flash('success', 'Compte créé avec succès. Vous pouvez vous connecter.');
                $this->redirect('index.php?page=login');
            }
        }
        $this->render('register', 'E-Tounsi - Inscription', 'register', compact('errors', 'old'));
    }

    public function logout()
    {
        $this->auth->logout($_GET['token'] ?? null);
    }

    public function profil()
    {
        AuthController::requireLogin();

        $errors = [];
        $old = [];
        $form = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form = $_POST['form'] ?? '';
            if ($form === 'infos') {
                $errors = $this->auth->updateProfile($_POST);
                $old = UserController::clean($_POST);
                $ok = 'Vos informations ont été mises à jour.';
            } elseif ($form === 'password') {
                $errors = $this->auth->changePassword($_POST);
                $ok = 'Votre mot de passe a été modifié.';
            } elseif ($form === 'delete') {
                $errors = $this->auth->deleteOwnAccount($_POST);
                if (!$errors) {
                    $this->redirect('index.php?page=home');
                }
            }
            if (!$errors && isset($ok)) {
                AuthController::flash('success', $ok);
                $this->redirect('index.php?page=profil');
            }
        }

        $user = $this->auth->getCurrentUser();
        $this->render('profil', 'E-Tounsi - Mon profil', 'profil', compact('errors', 'old', 'form', 'user'));
    }
}
