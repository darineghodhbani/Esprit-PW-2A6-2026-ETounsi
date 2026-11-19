<?php

class Controller
{
    protected function render($view, $title, $section, $data = [])
    {
        $flash = null;
        if (isset($_SESSION['poste_admin_flash'])) {
            $flash = $_SESSION['poste_admin_flash'];
            unset($_SESSION['poste_admin_flash']);
        }

        $csrf = csrfToken();
        $compteConnecte = utilisateurConnecte();
        $roleConnecte = 'Administrateur';

        // Le contenu de la vue est placé dans le layout commun de Spark Admin.
        ob_start();
        require __DIR__ . '/../views/admin/' . $view . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/layouts/admin.php';
    }

    public function problem($code, $title, $message, $section = 'dashboard')
    {
        http_response_code($code);
        $this->render('problem', $title, $section, ['message' => $message]);
    }

    protected function csrfValid()
    {
        $token = $this->text($_POST, 'csrf');
        if (csrfValide($token)) {
            return true;
        }

        $this->problem(403, 'Session expirée', 'Rechargez le formulaire avant de réessayer.');
        return false;
    }

    protected function text($source, $field)
    {
        if (isset($source[$field]) && is_string($source[$field])) {
            return trim($source[$field]);
        }
        return '';
    }

    protected function id($source, $field = 'id')
    {
        $id = filter_var($this->text($source, $field), FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return null;
        }
        return $id;
    }

    protected function redirect($page, $message, $type = 'success')
    {
        $_SESSION['poste_admin_flash'] = ['message' => $message, 'type' => $type];
        header('Location: ' . adminUrl($page), true, 303);
        exit;
    }

    protected function matches($query, $text)
    {
        if ($query === '') {
            return true;
        }
        return mb_stripos($text, $query, 0, 'UTF-8') !== false;
    }
}
