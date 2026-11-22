<?php



class Controller {

    public function renderBack(string $vue, array $data = []): void {
        extract($data);

        ob_start();
        require __DIR__ . '/../Views/backoffice/pages/' . $vue . '.php';
        $content = ob_get_clean();
        if (strpos($vue, 'poste/') === 0) $content = '<main id="contenu" class="poste-admin">' . $content . '</main>';

        require __DIR__ . '/../Views/backoffice/layout.php';
    }

    public function renderFront(string $vue, array $data = []): void {
        extract($data);
        if (strpos($vue, 'poste/') === 0) {
            ob_start();
            require __DIR__ . '/../Views/frontoffice/' . $vue . '.php';
            $content = ob_get_clean();
            require __DIR__ . '/../Views/frontoffice/layout.php';
        } else {
            require __DIR__ . '/../Views/frontoffice/' . $vue . '.php';
        }
    }
}
