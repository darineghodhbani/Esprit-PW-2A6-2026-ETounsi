<?php

require_once __DIR__ . '/UserController.php';

class AuthController
{
    private UserController $users;

    private const MAX_ATTEMPTS = 5;
    private const LOCK_TIME    = 300;

    public function __construct()
    {
        $this->users = new UserController();
    }

    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function hasRole(string $role): bool
    {
        return self::isAuthenticated() && ($_SESSION['role'] ?? null) === $role;
    }

    public static function requireLogin(): void
    {
        if (!self::isAuthenticated()) {
            self::flash('warning', 'Veuillez vous connecter pour accéder à cette page.');
            header('Location: index.php?page=login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        if (($_SESSION['role'] ?? null) !== $role) {
            http_response_code(403);
            $pageTitle = 'E-Tounsi - Accès refusé';
            $currentPage = '';
            $csrfToken = self::getCsrfToken();
            $flash = null;
            require __DIR__ . '/../Views/403.php';
            exit;
        }
    }

    public function refreshSession(): void
    {
        if (!self::isAuthenticated()) {
            return;
        }
        $user = $this->users->findById((int) $_SESSION['user_id']);
        if ($user === null || $user->getStatut() !== 'actif') {
            $this->destroySession();
            session_start();
            self::flash('danger', 'Votre compte n\'est plus accessible. Contactez l\'administration.');
            return;
        }
        $this->storeUserInSession($user);
    }

    private function storeUserInSession(User $user): void
    {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['nom']     = $user->getNom();
        $_SESSION['prenom']  = $user->getPrenom();
        $_SESSION['email']   = $user->getEmail();
        $_SESSION['role']    = $user->getRole();
    }

    private function destroySession(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    public static function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        return $token !== null && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function register(array $post): array
    {
        $data = UserController::clean($post);

        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            return [['global' => 'Jeton de sécurité invalide. Veuillez réessayer.'], $data];
        }

        $errors = $this->users->validate($data);
        if ($errors) {
            return [$errors, $data];
        }

        $user = new User();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setTelephone($data['telephone']);
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        $user->setRole('user');
        $user->setStatut('actif');
        $this->users->create($user);

        return [[], $data];
    }

    public function login(array $post): array
    {
        $email    = strtolower(trim((string) ($post['email'] ?? '')));
        $password = (string) ($post['password'] ?? '');
        $data     = ['email' => $email];

        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            return [['global' => 'Jeton de sécurité invalide. Veuillez réessayer.'], $data];
        }

        $lockedUntil = $_SESSION['login_locked_until'] ?? 0;
        if ($lockedUntil > time()) {
            $min = (int) ceil(($lockedUntil - time()) / 60);
            return [['global' => "Trop de tentatives. Réessayez dans $min minute(s)."], $data];
        }

        $errors = [];
        if ($email === '') {
            $errors['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide.";
        }
        if ($password === '') {
            $errors['password'] = 'Le mot de passe est obligatoire.';
        }
        if ($errors) {
            return [$errors, $data];
        }

        $user = $this->users->findByEmail($email);
        if ($user === null || !password_verify($password, $user->getPassword())) {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            if ($_SESSION['login_attempts'] >= self::MAX_ATTEMPTS) {
                $_SESSION['login_locked_until'] = time() + self::LOCK_TIME;
                $_SESSION['login_attempts'] = 0;
            }

            return [['global' => 'Email ou mot de passe incorrect.'], $data];
        }

        if ($user->getStatut() !== 'actif') {
            return [['global' => 'Votre compte est bloqué. Contactez l\'administration.'], $data];
        }

        session_regenerate_id(true);
        unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
        $this->storeUserInSession($user);
        $_SESSION['last_activity'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        return [[], $data];
    }

    public function logout(?string $token): void
    {
        if (self::verifyCsrfToken($token)) {
            $this->destroySession();
            session_start();
            self::flash('success', 'Vous êtes déconnecté.');
        }
        header('Location: index.php?page=login');
        exit;
    }

    public function getCurrentUser(): ?User
    {
        return self::isAuthenticated() ? $this->users->findById((int) $_SESSION['user_id']) : null;
    }

    public function updateProfile(array $post): array
    {
        $current = $this->getCurrentUser();
        $data = UserController::clean($post);

        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ['global' => 'Jeton de sécurité invalide. Veuillez réessayer.'];
        }

        $errors = $this->users->validate($data, $current->getId(), false);
        unset($errors['password'], $errors['password_confirm']);
        if ($errors) {
            return $errors;
        }

        $current->setNom($data['nom']);
        $current->setPrenom($data['prenom']);
        $current->setEmail($data['email']);
        $current->setTelephone($data['telephone']);

        $this->users->update($current);
        $this->storeUserInSession($current);
        return [];
    }

    public function changePassword(array $post): array
    {
        $current = $this->getCurrentUser();
        $old     = (string) ($post['old_password'] ?? '');
        $new     = (string) ($post['password'] ?? '');
        $confirm = (string) ($post['password_confirm'] ?? '');

        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ['global' => 'Jeton de sécurité invalide. Veuillez réessayer.'];
        }

        $errors = [];
        if ($old === '') {
            $errors['old_password'] = "L'ancien mot de passe est obligatoire.";
        } elseif (!password_verify($old, $current->getPassword())) {
            $errors['old_password'] = "L'ancien mot de passe est incorrect.";
        }
        $pwdError = UserController::passwordError($new);
        if ($pwdError !== null) {
            $errors['password'] = $pwdError;
        } elseif ($new !== $confirm) {
            $errors['password_confirm'] = 'Les deux mots de passe ne sont pas identiques.';
        }
        if ($errors) {
            return $errors;
        }

        $this->users->updatePassword($current->getId(), $new);
        session_regenerate_id(true);
        return [];
    }

    public function deleteOwnAccount(array $post): array
    {
        $current = $this->getCurrentUser();
        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ['global' => 'Jeton de sécurité invalide. Veuillez réessayer.'];
        }
        if ($current->getRole() === 'admin') {
            return ['global' => 'Un administrateur ne peut pas supprimer son compte ici.'];
        }
        if (!password_verify((string) ($post['delete_password'] ?? ''), $current->getPassword())) {
            return ['delete_password' => 'Mot de passe incorrect.'];
        }
        $this->users->delete($current->getId());
        $this->destroySession();
        session_start();
        self::flash('success', 'Votre compte a été supprimé.');
        return [];
    }
}
