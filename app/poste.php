<?php

function pagesFrontPoste()
{
    return ['accueil' => 'module', 'connexion' => 'login', 'inscription' => 'signup',
        'profil' => 'profile', 'suivi' => 'suivi-colis', 'envoi' => 'declarer-envoi', 'historique' => 'mes-colis'];
}

function frontUrl($page = 'accueil', $parametres = [], $langue = null)
{
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
            return t('poste.statut.en_attente');
        case 'Accepté':
            return t('poste.statut.accepte');
        case 'En transit':
            return t('poste.statut.en_transit');
        case 'Livré':
            return t('poste.statut.livre');
        case 'Annulé':
            return t('poste.statut.annule');
        default:
            return $statut;
    }
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

function posteMatches($query, $text)
{
    if ($query === '') {
        return true;
    }
    return mb_stripos($text, $query, 0, 'UTF-8') !== false;
}
