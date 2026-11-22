<?php

function pagesFrontPoste()
{
    return ['accueil' => 'module', 'connexion' => 'login', 'inscription' => 'signup',
        'profil' => 'profile', 'suivi' => 'suivi-colis', 'envoi' => 'declarer-envoi', 'historique' => 'mes-colis'];
}

function tr($francais, $arabe)
{
    return et_lang() === 'ar' ? $arabe : $francais;
}

function frontUrl($page = 'accueil', $parametres = [], $langue = null)
{
    global $language;

    $page = pagesFrontPoste()[$page] ?? $page;

    if ($langue === null) {
        $langue = et_lang();
    }

    $query = ['page' => $page, 'lang' => $langue];
    if ($page === 'module') $query['id'] = 'poste';
    foreach ($parametres as $nom => $valeur) {
        $query[$nom] = $valeur;
    }

    return 'index.php?' . http_build_query($query);
}

function parcelStatus($statut)
{
    switch ($statut) {
        case 'En attente':
            return tr('En attente', 'قيد الانتظار');
        case 'Accepté':
            return tr('Accepté', 'تمّ القبول');
        case 'En transit':
            return tr('En transit', 'قيد النقل');
        case 'Livré':
            return tr('Livré', 'تم التسليم');
        case 'Annulé':
            return tr('Annulé', 'ملغى');
        default:
            return $statut;
    }
}

function adminUrl($page = 'dashboard', $parametres = []) {
    $pages = ['bureaux' => 'admin-bureaux', 'bureauList' => 'admin-bureaux', 'bureauliste' => 'admin-bureaux',
              'colis' => 'admin-colis', 'colisList' => 'admin-colis', 'Colisliste' => 'admin-colis',
              'utilisateurs' => 'admin-utilisateurs', 'userList' => 'admin-utilisateurs',
              'utilisateur-form' => 'admin-utilisateur-form', 'utilisateur-suppression' => 'admin-utilisateur-delete'];
    $query = ['page' => $pages[$page] ?? $page];
    return 'index.php?' . http_build_query(array_merge($query, $parametres));
}

function posteRender($view, $title, $section, $data = []) {
    if (isset($_SESSION['poste_admin_flash'])) {
        $_SESSION['flash'] = $_SESSION['poste_admin_flash'];
        unset($_SESSION['poste_admin_flash']);
    }
    $pages = ['bureaux' => 'admin-bureaux', 'colis' => 'admin-colis', 'utilisateurs' => 'admin-utilisateurs'];
    (new Controller())->renderBack('poste/' . $view, ['title' => $title, 'currentPage' => $pages[$section] ?? 'dashboard',
        'section' => $section, 'data' => $data, 'csrf' => csrfToken(), 'compteConnecte' => utilisateurConnecte()]);
}

function posteFront($vue, $titre, $data = []) {
    $pages = ['bureaux' => 'bureaux-poste', 'suiviColis' => 'suivi-colis', 'declarerEnvoi' => 'declarer-envoi', 'mesColis' => 'mes-colis'];
    $pageCourante = $pages[$vue] ?? 'module';
    if (in_array($_GET['page'] ?? '', ['modifier-colis', 'supprimer-colis'], true)) $pageCourante = 'mes-colis';
    (new Controller())->renderFront('poste/' . $vue, array_merge(['title' => $titre, 'pageTitle' => $titre,
        'currentPage' => $pageCourante, 'language' => et_lang(), 'jeton' => csrfToken(),
        'utilisateur' => utilisateurConnecte(), 'erreur' => ''], $data));
}

function posteProblem($code, $title, $message, $section = 'dashboard')
{
    http_response_code($code);
    posteRender('problem', $title, $section, ['message' => $message]);
}

function posteCsrfValid()
{
    $token = posteText($_POST, 'csrf');
    if (csrfValide($token)) {
        return true;
    }

    posteProblem(403, 'Session expirée', 'Rechargez le formulaire avant de réessayer.');
    return false;
}

function posteText($source, $field)
{
    if (isset($source[$field]) && is_string($source[$field])) {
        return trim($source[$field]);
    }
    return '';
}

function posteId($source, $field = 'id')
{
    $id = filter_var(posteText($source, $field), FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        return null;
    }
    return $id;
}

function posteRedirect($page, $message, $type = 'success')
{
    $_SESSION['poste_admin_flash'] = ['message' => $message, 'type' => $type];
    header('Location: ' . adminUrl($page), true, 303);
    exit;
}

function posteMatches($query, $text)
{
    if ($query === '') {
        return true;
    }
    return mb_stripos($text, $query, 0, 'UTF-8') !== false;
}
