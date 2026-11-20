<?php

function tr($francais, $arabe)
{
    global $language;

    if ($language === 'ar') {
        return $arabe;
    }
    return $francais;
}

function frontUrl($page = 'accueil', $parametres = [], $langue = null)
{
    global $language;

    if ($langue === null) {
        $langue = $language ?? 'fr';
    }

    $query = ['page' => $page, 'lang' => $langue];
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

function adminUrl($page = 'dashboard', $parametres = [])
{
    $query = ['espace' => 'admin', 'page' => $page];
    foreach ($parametres as $nom => $valeur) {
        $query[$nom] = $valeur;
    }
    return 'index.php?' . http_build_query($query);
}

function gestionPoste()
{
    return new PosteController(Config::getConnexion());
}

function e($valeur)
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
