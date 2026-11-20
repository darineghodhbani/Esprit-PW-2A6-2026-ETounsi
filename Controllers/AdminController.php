<?php

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/UserController.php';
require_once __DIR__ . '/ProprieteController.php';
require_once __DIR__ . '/ImpotController.php';

class AdminController
{
    private UserController $users;
    private ProprieteController $proprietes;
    private ImpotController $impots;

    public function __construct()
    {
        AuthController::requireRole('admin');
        $this->users = new UserController();
        $this->proprietes = new ProprieteController();
        $this->impots = new ImpotController();
    }

    private function render($view, $title, $currentPage, array $data = [])
    {
        extract($data);
        $csrfToken = AuthController::getCsrfToken();
        $flash = AuthController::getFlash();

        ob_start();
        require __DIR__ . '/../Views/backoffice/pages/' . $view . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/backoffice/layout.php';
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    private function checkPostAndCsrf(string $back = 'admin.php?page=users'): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            AuthController::flash('danger', 'Action refusée (jeton de sécurité invalide).');
            $this->redirect($back);
        }
    }

    public function dashboard()
    {
        $userStats = [
            'total'   => $this->users->countAll(),
            'users'   => $this->users->countBy('role', 'user'),
            'bloques' => $this->users->countBy('statut', 'bloque'),
        ];
        $this->render('dashboard', 'E-Tounsi Admin - Tableau de bord', 'dashboard', compact('userStats'));
    }

    public function proprietes()
    {
        $list = $this->proprietes->findAll();
        $total = $this->proprietes->countAll();
        $this->render('proprietes', 'E-Tounsi Admin - Propriétés', 'proprietes', compact('list', 'total'));
    }

    public function proprieteForm()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $propriete = $id ? $this->proprietes->findById($id) : new Propriete();
        if ($propriete === null) {
            AuthController::flash('danger', 'Propriété introuvable.');
            $this->redirect('admin.php?page=proprietes');
        }

        $errors = [];
        $old = [
            'id_user'   => $propriete->getIdUser(),
            'type'      => $propriete->getType(),
            'reference' => $propriete->getReference(),
            'adresse'   => $propriete->getAdresse(),
            'valeur'    => $id ? (string) $propriete->getValeur() : '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = ProprieteController::clean($_POST);
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $errors = $this->proprietes->validate($old);
            }

            if (!$errors) {
                $propriete->setIdUser($old['id_user']);
                $propriete->setType($old['type']);
                $propriete->setReference($old['reference']);
                $propriete->setAdresse($old['adresse']);
                $propriete->setValeur((int) $old['valeur']);

                if ($id === 0) {
                    $this->proprietes->create($propriete);
                    AuthController::flash('success', 'Propriété ajoutée avec succès.');
                } else {
                    $this->proprietes->update($propriete);
                    AuthController::flash('success', 'Propriété modifiée avec succès.');
                }
                $this->redirect('admin.php?page=proprietes');
            }
        }

        $citoyens = $this->users->findAll('', 'user');
        $title = $id ? 'Modifier une propriété' : 'Ajouter une propriété';
        $this->render('propriete-form', 'E-Tounsi Admin - ' . $title, 'proprietes',
            compact('id', 'old', 'errors', 'title', 'citoyens'));
    }

    public function proprieteDelete()
    {
        $this->checkPostAndCsrf('admin.php?page=proprietes');
        $id = (int) ($_POST['id'] ?? 0);
        $this->proprietes->delete($id);
        AuthController::flash('success', 'Propriété supprimée.');
        $this->redirect('admin.php?page=proprietes');
    }

    public function impots()
    {
        $list = $this->impots->findAll();
        $nbPayes = $this->impots->countByStatut('paye');
        $nbNonPayes = $this->impots->countByStatut('non_paye');
        $nbEnAttente = $this->impots->countByStatut('en_attente');
        $this->render('impots', 'E-Tounsi Admin - Impôts', 'impots', compact('list', 'nbPayes', 'nbNonPayes', 'nbEnAttente'));
    }

    public function impotForm()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $impot = $id ? $this->impots->findById($id) : new Impot();
        if ($impot === null) {
            AuthController::flash('danger', 'Impôt introuvable.');
            $this->redirect('admin.php?page=impots');
        }

        $errors = [];
        $old = [
            'id_propriete' => $impot->getIdPropriete(),
            'annee'        => $id ? (string) $impot->getAnnee() : '',
            'montant'      => $id ? (string) $impot->getMontant() : '',
            'date_limite'  => $impot->getDateLimite(),
            'statut'       => $impot->getStatut(),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = ImpotController::clean($_POST);
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $errors = $this->impots->validate($old);
            }

            if (!$errors) {
                $impot->setIdPropriete($old['id_propriete']);
                $impot->setAnnee((int) $old['annee']);
                $impot->setMontant((int) $old['montant']);
                $impot->setDateLimite($old['date_limite']);
                $impot->setStatut($old['statut']);

                if ($id === 0) {
                    $this->impots->create($impot);
                    AuthController::flash('success', 'Impôt ajouté avec succès.');
                } else {
                    $this->impots->update($impot);
                    AuthController::flash('success', 'Impôt modifié avec succès.');
                }
                $this->redirect('admin.php?page=impots');
            }
        }

        $proprietes = $this->proprietes->findAll();
        $title = $id ? 'Modifier un impôt' : 'Ajouter un impôt';
        $this->render('impot-form', 'E-Tounsi Admin - ' . $title, 'impots',
            compact('id', 'old', 'errors', 'title', 'proprietes'));
    }

    public function impotDelete()
    {
        $this->checkPostAndCsrf('admin.php?page=impots');
        $id = (int) ($_POST['id'] ?? 0);
        $this->impots->delete($id);
        AuthController::flash('success', 'Impôt supprimé.');
        $this->redirect('admin.php?page=impots');
    }

    public function users()
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $role   = (string) ($_GET['role'] ?? '');
        $statut = (string) ($_GET['statut'] ?? '');

        $list = $this->users->findAll($search, $role, $statut);
        $stats = [
            'total'   => $this->users->countAll(),
            'admins'  => $this->users->countBy('role', 'admin'),
            'users'   => $this->users->countBy('role', 'user'),
            'bloques' => $this->users->countBy('statut', 'bloque'),
        ];
        $this->render('users', 'E-Tounsi Admin - Utilisateurs', 'users',
            compact('list', 'stats', 'search', 'role', 'statut'));
    }

    public function userShow()
    {
        $user = $this->users->findById((int) ($_GET['id'] ?? 0));
        if ($user === null) {
            AuthController::flash('danger', 'Utilisateur introuvable.');
            $this->redirect('admin.php?page=users');
        }
        $this->render('user-show', 'E-Tounsi Admin - Détail utilisateur', 'users', compact('user'));
    }

    public function userForm()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $id ? $this->users->findById($id) : new User();
        if ($user === null) {
            AuthController::flash('danger', 'Utilisateur introuvable.');
            $this->redirect('admin.php?page=users');
        }
        $isSelf = $id === (int) $_SESSION['user_id'];

        $errors = [];
        $old = [
            'nom' => $user->getNom(), 'prenom' => $user->getPrenom(), 'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(), 'role' => $user->getRole(), 'statut' => $user->getStatut(),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = UserController::clean($_POST);
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } else {
                $errors = $this->users->validate($old, $id, $id === 0, true);

                if ($isSelf && ($old['role'] !== 'admin' || $old['statut'] !== 'actif')) {
                    $errors['role'] = 'Vous ne pouvez pas modifier votre propre rôle ou statut.';
                }
            }

            if (!$errors) {
                $user->setNom($old['nom']);
                $user->setPrenom($old['prenom']);
                $user->setEmail($old['email']);
                $user->setTelephone($old['telephone']);
                $user->setRole($old['role']);
                $user->setStatut($old['statut']);

                if ($id === 0) {
                    $user->setPassword(password_hash($old['password'], PASSWORD_DEFAULT));
                    $this->users->create($user);
                    AuthController::flash('success', 'Utilisateur ajouté avec succès.');
                } else {
                    $this->users->update($user);
                    if ($old['password'] !== '') {
                        $this->users->updatePassword($id, $old['password']);
                    }
                    AuthController::flash('success', 'Utilisateur modifié avec succès.');
                }
                $this->redirect('admin.php?page=users');
            }
        }

        $title = $id ? 'Modifier un utilisateur' : 'Ajouter un utilisateur';
        $this->render('user-form', 'E-Tounsi Admin - ' . $title, 'users',
            compact('id', 'old', 'errors', 'title', 'isSelf'));
    }

    public function userToggle()
    {
        $this->checkPostAndCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $user = $this->users->findById($id);

        if ($user === null) {
            AuthController::flash('danger', 'Utilisateur introuvable.');
        } elseif ($id === (int) $_SESSION['user_id']) {
            AuthController::flash('danger', 'Vous ne pouvez pas bloquer votre propre compte.');
        } else {
            $new = $user->getStatut() === 'actif' ? 'bloque' : 'actif';
            $this->users->setStatut($id, $new);
            AuthController::flash('success', 'Compte ' . ($new === 'actif' ? 'débloqué.' : 'bloqué.'));
        }
        $this->redirect('admin.php?page=users');
    }

    public function userDelete()
    {
        $this->checkPostAndCsrf();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id === (int) $_SESSION['user_id']) {
            AuthController::flash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
        } elseif ($this->users->findById($id) === null) {
            AuthController::flash('danger', 'Utilisateur introuvable.');
        } else {
            $this->users->delete($id);
            AuthController::flash('success', 'Utilisateur supprimé.');
        }
        $this->redirect('admin.php?page=users');
    }
}
