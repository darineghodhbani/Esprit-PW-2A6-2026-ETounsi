<?php

require_once __DIR__ . '/Models/Citoyen.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('ETOUNSI_INTEGRATION_SESSION');
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
    deconnecterUtilisateur('Session expirée.');
    $_SESSION['session_expiree'] = true;
}
$_SESSION['last_activity'] = $maintenant;

header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com; frame-src https://www.google.com/recaptcha/ https://recaptcha.google.com/recaptcha/; connect-src 'self' https://www.google.com/recaptcha/; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");

function gestionUtilisateurs() {
    return new Citoyen();
}

function utilisateurConnecte() {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $utilisateur = gestionUtilisateurs()->trouver((int) $_SESSION['user_id']);
    if ($utilisateur === null || $utilisateur['actif'] !== 1
        || $utilisateur['version_session'] !== ($_SESSION['version_session'] ?? null)) {
        deconnecterUtilisateur('Session révoquée ou compte indisponible.');
        return null;
    }
    $_SESSION['nom'] = $utilisateur['nom'];
    $_SESSION['prenom'] = $utilisateur['prenom'];
    $_SESSION['role'] = $utilisateur['role'];
    return $utilisateur;
}

function exigerConnexion() {
    $utilisateur = utilisateurConnecte();
    if ($utilisateur === null) {
        header('Location: ' . frontUrl('connexion'));
        exit;
    }
    return $utilisateur;
}

function exigerRole($roles) {
    $utilisateur = exigerConnexion();
    if (!in_array($utilisateur['role'], $roles, true)) {
        auditEvenement('ACCESS_DENIED', 'Rôle insuffisant pour la page ' . pageAudit() . '.');
        http_response_code(403);
        echo '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Accès refusé · E-Tounsi</title><main><h1>Accès refusé</h1><p>Votre rôle ne permet pas d’accéder à cette page.</p>';
        echo '<a href="index.php">Revenir à l’accueil</a></main></html>';
        exit;
    }
    return $utilisateur;
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfValide($token) {
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function connecterUtilisateur($utilisateur) {
    session_regenerate_id(true);
    $_SESSION = [
        'user_id' => (int) $utilisateur['id'],
        'id_citoyen' => (int) $utilisateur['id'],
        'prenom' => $utilisateur['prenom'] ?? '',
        'lang' => $_SESSION['lang'] ?? 'fr',
        'nom' => $utilisateur['nom'],
        'role' => $utilisateur['role'],
        'version_session' => (int) $utilisateur['version_session'],
        'connected_at' => time(),
        'last_activity' => time(),
    ];
}

function deconnecterUtilisateur($raison = 'Déconnexion de la session.') {
    $langue = $_SESSION['lang'] ?? 'fr';
    if (!empty($_SESSION['user_id'])) auditEvenement('LOGOUT', $raison, (int) $_SESSION['user_id']);
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
    // Une nouvelle session permet de présenter un formulaire de connexion protégé.
    session_id('');
    session_start();
    $_SESSION['lang'] = in_array($langue, ['fr', 'ar'], true) ? $langue : 'fr';
}


function pageAudit() {
    $page = $_GET['page'] ?? 'home';
    return is_string($page) && preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $page) ? $page : 'inconnue';
}

function auditEvenement($action, $details, $cible = null) {
    try {
        (new AuditLog())->log($_SESSION['user_id'] ?? null, $action, $details, $cible);
    } catch (PDOException $erreur) {
        // Une panne du journal ne doit pas empêcher un refus d'accès ou une déconnexion.
        error_log('Audit indisponible : ' . $erreur->getMessage());
    }
}
