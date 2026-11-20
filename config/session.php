<?php

require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    session_start();
}

const SESSION_TIMEOUT = 1800;

if (isset($_SESSION['user_id'], $_SESSION['last_activity'])
    && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    $_SESSION = [];
    session_destroy();
    session_start();
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Votre session a expiré. Veuillez vous reconnecter.'];
}
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

header("Content-Security-Policy: default-src 'self'; "
    . "script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
    . "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
    . "img-src 'self' data:; frame-src https://www.google.com; "
    . "frame-ancestors 'none'; form-action 'self'; base-uri 'self'");
