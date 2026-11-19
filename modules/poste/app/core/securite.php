<?php

require_once __DIR__ . '/../controllers/AuthController.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('ETOUNSISESSID');
    $connexionHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $connexionHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$maintenant = time();
$sessionExpiree = isset($_SESSION['last_activity']) && $maintenant - $_SESSION['last_activity'] > 1800;
$connexionExpiree = isset($_SESSION['connected_at']) && $maintenant - $_SESSION['connected_at'] > 28800;
if ($sessionExpiree || $connexionExpiree) {
    deconnecterUtilisateur();
    $_SESSION['session_expiree'] = true;
}
$_SESSION['last_activity'] = $maintenant;

header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");

function gestionUtilisateurs()
{
    return new AuthController(Config::getConnexion());
}

function utilisateurConnecte()
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $utilisateur = gestionUtilisateurs()->trouver((int) $_SESSION['user_id']);
    if ($utilisateur === null || $utilisateur['actif'] !== 1
        || $utilisateur['version_session'] !== ($_SESSION['version_session'] ?? null)) {
        deconnecterUtilisateur();
        return null;
    }
    $_SESSION['nom'] = $utilisateur['nom'];
    $_SESSION['role'] = $utilisateur['role'];
    return $utilisateur;
}

function exigerConnexion()
{
    $utilisateur = utilisateurConnecte();
    if ($utilisateur === null) {
        header('Location: ' . frontUrl('connexion'));
        exit;
    }
    return $utilisateur;
}

function exigerRole($roles)
{
    $utilisateur = exigerConnexion();
    if (!in_array($utilisateur['role'], $roles, true)) {
        http_response_code(403);
        echo '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Accès refusé · E-Tounsi</title><main><h1>Accès refusé</h1><p>Votre rôle ne permet pas d’accéder à cette page.</p>';
        echo '<a href="index.php">Revenir à l’accueil</a></main></html>';
        exit;
    }
    return $utilisateur;
}

function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfValide($token)
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function connecterUtilisateur($utilisateur)
{
    session_regenerate_id(true);
    $_SESSION = [
        'user_id' => (int) $utilisateur['id'],
        'nom' => $utilisateur['nom'],
        'role' => $utilisateur['role'],
        'version_session' => (int) $utilisateur['version_session'],
        'connected_at' => time(),
        'last_activity' => time(),
    ];
}

function deconnecterUtilisateur()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $parametres = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $parametres['path'],
            'domain' => $parametres['domain'],
            'secure' => $parametres['secure'],
            'httponly' => $parametres['httponly'],
            'samesite' => 'Lax',
        ]);
    }
    session_destroy();
    // Une nouvelle session vide permet de présenter un formulaire de connexion protégé.
    session_id('');
    session_start();
}
