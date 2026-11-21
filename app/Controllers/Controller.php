<?php

require_once __DIR__ . '/CitoyenController.php';

class Controller {

    protected function renderBack(string $vue, array $data = []): void {
        extract($data);

        ob_start();
        require __DIR__ . '/../Views/backoffice/pages/' . $vue . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/backoffice/layout.php';
    }

    protected function renderFront(string $vue, array $data = []): void {
        extract($data);
        require __DIR__ . '/../Views/frontoffice/' . $vue . '.php';
    }
}
