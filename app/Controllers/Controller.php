<?php



class Controller {

    public function renderBack(string $vue, array $data = []): void {
        $csrf = csrfToken();
        $compteConnecte = utilisateurConnecte();
        extract($data);

        ob_start();
        require __DIR__ . '/../Views/backoffice/pages/' . $vue . '.php';
        $content = ob_get_clean();
        if (strpos($vue, 'poste/') === 0 || strpos($vue, 'utilisateurs/') === 0 || strpos($vue, 'commun/') === 0) {
            $content = '<main id="contenu" class="poste-admin">' . $content . '</main>';
        }

        require __DIR__ . '/../Views/backoffice/layout.php';
    }

    public function renderFront(string $vue, array $data = []): void {
        $language = et_lang();
        $jeton = csrfToken();
        $utilisateur = utilisateurConnecte();
        $pageTitle = $data['title'] ?? 'E-Tounsi';
        extract($data);
        if (strpos($vue, 'poste/') === 0 || strpos($vue, 'cnam/') === 0 || strpos($vue, 'transport/') === 0) {
            ob_start();
            require __DIR__ . '/../Views/frontoffice/' . $vue . '.php';
            $content = ob_get_clean();
            require __DIR__ . '/../Views/frontoffice/layout.php';
        } else {
            require __DIR__ . '/../Views/frontoffice/' . $vue . '.php';
        }
    }
}
