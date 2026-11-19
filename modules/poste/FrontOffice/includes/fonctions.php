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
        $langue = $language;
    }

    $query = ['page' => $page, 'lang' => $langue];
    foreach ($parametres as $nom => $valeur) {
        $query[$nom] = $valeur;
    }

    return './?' . http_build_query($query);
}

function parcelStatus($statut)
{
    switch ($statut) {
        case 'En attente':
            return tr('En attente', 'قيد الانتظار');
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
