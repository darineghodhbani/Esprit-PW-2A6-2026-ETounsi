<?php
// Présentation commune au portail et au module Poste.
function etE($texte)
{
    return htmlspecialchars((string) $texte, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function etLang()
{
    global $language;
    return ($language ?? ($_GET['lang'] ?? 'fr')) === 'ar' ? 'ar' : 'fr';
}

function etT($fr, $ar)
{
    return etLang() === 'ar' ? $ar : $fr;
}

function etBase()
{
    return defined('ETOUNSI_PORTAIL') ? '' : '../../';
}

function etAsset($chemin)
{
    return etBase() . 'modules/poste/public/assets/' . $chemin;
}

function etHome($ancre = '')
{
    return etBase() . 'index.php?lang=' . etLang() . $ancre;
}

function etPoste($page = 'accueil', $admin = false)
{
    $params = ['page' => $page, 'lang' => etLang()];
    if ($admin) {
        $params['espace'] = 'admin';
    }
    return etBase() . 'modules/poste/index.php?' . http_build_query($params);
}

function etServices()
{
    return [
        'transport' => [
            'nom' => etT('Transport', 'النقل'), 'icone' => 'bus-front',
            'image' => 'transport-office', 'court' => etT('Se déplacer', 'التنقّل'),
            'description' => etT('Préparez vos déplacements et retrouvez les informations de transport.', 'حضّر تنقّلاتك واطّلع على معلومات النقل.'),
            'mots' => 'transport bus metro train tramway déplacement abonnement نقل مترو حافلة قطار',
        ],
        'municipalite' => [
            'nom' => etT('Municipalité', 'البلدية'), 'icone' => 'buildings',
            'image' => 'municipalite-office', 'court' => etT('Ma ville au quotidien', 'مدينتي يومًا بيوم'),
            'description' => etT('Retrouvez vos démarches et les services de votre municipalité.', 'اطّلع على إجراءات وخدمات بلديّتك.'),
            'mots' => 'municipalite mairie extrait naissance etat civil acte permis بلدية ولادة وثيقة',
        ],
        'cnam' => [
            'nom' => etT('Assurance publique — CNAM', 'التأمين العمومي — CNAM'), 'icone' => 'heart-pulse',
            'image' => 'cnam-office', 'court' => etT('Prendre soin de vous', 'صحّتك أولويّة'),
            'description' => etT('Un espace consacré à l’assurance maladie et à vos démarches CNAM.', 'فضاء للتأمين على المرض وإجراءات الصندوق الوطني للتأمين على المرض.'),
            'mots' => 'cnam assurance maladie sante santé soin remboursement carte تأمين صحة علاج',
        ],
        'poste' => [
            'nom' => etT('Poste', 'البريد'), 'icone' => 'envelope',
            'image' => 'bureau-poste', 'court' => etT('Vos envois, tout simplement', 'إرسالياتك بكلّ سهولة'),
            'description' => etT('Déclarez un colis, consultez son état et trouvez votre bureau de poste.', 'صرّح بطردك وتتبّع حالته واعثر على مكتب بريدك.'),
            'mots' => 'poste colis suivi envoi bureau postal livraison reference بريد طرد تتبع إرسال',
        ],
        'finances' => [
            'nom' => etT('Recette des Finances', 'قباضة المالية'), 'icone' => 'bank',
            'image' => 'finances-office', 'court' => etT('Mes démarches fiscales', 'إجراءاتي الجبائيّة'),
            'description' => etT('Retrouvez l’espace dédié à vos démarches auprès de la recette des finances.', 'فضاء لإجراءاتك لدى قباضة المالية.'),
            'mots' => 'finances recette impot impôt fiscal paiement taxe timbre قباضة مالية ضريبة',
        ],
    ];
}
