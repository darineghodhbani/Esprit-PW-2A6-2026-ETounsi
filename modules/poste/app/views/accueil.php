<?php
$services = [
    'suivi' => [
        'icone' => 'search',
        'titre' => tr('Suivre un colis', 'تتبّع طردًا'),
        'description' => tr('Vérifiez le statut de votre envoi.', 'تحقّق من حالة إرساليتك.'),
    ],
    'bureaux' => [
        'icone' => 'geo-alt-fill',
        'titre' => tr('Bureaux de poste', 'مكاتب البريد'),
        'description' => tr('Trouvez un bureau par ville.', 'اعثر على مكتب حسب المدينة.'),
    ],
    'envoi' => [
        'icone' => 'box-seam-fill',
        'titre' => tr('Déclarer un envoi', 'التصريح بإرسال طرد'),
        'description' => tr('Obtenez un numéro de suivi.', 'احصل على رقم تتبّع.'),
    ],
    'historique' => [
        'icone' => 'clock',
        'titre' => tr('Mes colis', 'طرودي'),
        'description' => tr('Retrouvez vos envois.', 'اطّلع على إرسالياتك.'),
    ],
];
?>
<header class="page-header poste-hero">
    <div class="container">
        <h1><?= e($pageTitle) ?></h1>
        <p><?= e(tr('Suivez vos colis et trouvez votre bureau de poste.', 'تتبّع طرودك واعثر على مكتب بريدك.')) ?></p>
    </div>
</header>

<section class="container py-5" aria-labelledby="demarches">
    <div class="section-heading">
        <p class="section-label"><?= e(tr('Démarches', 'الإجراءات')) ?></p>
        <h2 id="demarches"><?= e(tr('Choisissez votre démarche', 'اختر الخدمة التي تحتاجها')) ?></h2>
    </div>
    <div class="grille-services">
        <?php foreach ($services as $page => $service): ?>
            <a class="service-item" href="<?= e(frontUrl($page)) ?>">
                <i class="bi bi-<?= e($service['icone']) ?> service-icon" aria-hidden="true"></i>
                <h3><?= e($service['titre']) ?></h3>
                <p><?= e($service['description']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-suivi py-5" aria-labelledby="suivi-titre">
    <div class="container">
        <div class="section-heading">
            <h2 id="suivi-titre"><?= e(tr('Suivre un colis', 'تتبّع طردًا')) ?></h2>
        </div>
        <div class="panel-narrow">
            <?php require __DIR__ . '/tracking-form.php'; ?>
        </div>
    </div>
</section>
