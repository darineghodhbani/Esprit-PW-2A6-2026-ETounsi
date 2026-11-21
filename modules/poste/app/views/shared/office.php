<?php $etIsPoste = ($etServiceKey ?? 'poste') === 'poste'; ?>
<noscript><style>.et-office-entry{display:none}.et-office-welcome{opacity:1;transform:none}</style></noscript>
<div class="et-office" data-office-panel data-office-page="<?= !empty($etOfficePage) ? 'true' : 'false' ?>" data-service="<?= etE($etServiceKey ?? 'poste') ?>">
    <section class="et-office-hall" aria-labelledby="office-title">
        <img class="et-office-photo" data-office-photo src="<?= etE(etAsset('images/portal/' . ($etService['image'] ?? 'bureau-poste') . '.webp')) ?>" alt="<?= etE(etT('Intérieur du bureau', 'داخل المكتب')) ?>">
        <div class="et-office-shade"></div>
        <div class="et-office-welcome">
            <span class="et-office-tag"><i class="bi bi-geo-alt" aria-hidden="true"></i> E-Tounsi · <?= etE(etT('Votre administration en ligne', 'إدارتك على الخطّ')) ?></span>
            <p><?= etE(etT('Bienvenue dans votre', 'مرحبًا بك في')) ?></p>
            <h2 id="office-title" data-office-title><?= etE($etService['nom'] ?? etT('Poste', 'البريد')) ?></h2>
            <p data-office-description><?= etE($etService['description'] ?? '') ?></p>
            <a class="et-button et-button-light" href="#office-access" data-office-scroll><span data-office-cta><?= etE(($etServiceKey ?? 'poste') === 'poste' ? etT('Bureau de poste', 'مكتب البريد') : etT('Découvrir ce service', 'اكتشف الخدمة')) ?></span><i class="bi bi-arrow-down" aria-hidden="true"></i></a>
        </div>
        <div class="et-office-entry et-office-entry--red" aria-hidden="true">
            <div class="et-door-left"><span class="et-red-door-outline"></span><span class="et-red-door-handle"></span></div>
            <div class="et-door-right"><span class="et-red-door-outline"></span><span class="et-red-door-handle"></span></div>
            <span class="et-visually-hidden" data-door-label><?= etE($etService['nom'] ?? etT('Poste', 'البريد')) ?></span>
        </div>
        <span class="et-visually-hidden" role="status" aria-live="polite" data-door-status data-opening="<?= etE(etT('La porte s’ouvre.', 'الباب بصدد الفتح.')) ?>" data-open="<?= etE(etT('Le bureau est ouvert.', 'المكتب مفتوح.')) ?>"></span>
    </section>
    <section class="et-office-access et-container" id="office-access" tabindex="-1">
        <div class="et-office-poste" data-office-poste <?= !$etIsPoste ? 'hidden' : '' ?>>
            <?php if ($utilisateur === null): ?>
                <div class="et-access-layout">
                    <div class="et-access-copy"><span class="et-eyebrow"><?= etE(etT('Votre espace Poste', 'فضاء البريد الخاصّ بك')) ?></span><h2><?= etE(etT('Tout commence par votre compte.', 'ابدأ بحسابك.')) ?></h2><p><?= etE(etT('Connectez-vous pour déclarer un envoi et retrouver tous vos colis au même endroit.', 'سجّل الدخول للتصريح بإرسال طرد وللاطّلاع على جميع طرودك في نفس الفضاء.')) ?></p><ul><li><?= etE(etT('Une référence pour chaque colis', 'رقم تتبّع لكلّ طرد')) ?></li><li><?= etE(etT('Un état mis à jour par le bureau', 'حالة يحيّنها المكتب')) ?></li><li><?= etE(etT('Vos déclarations dans votre espace', 'تصريحاتك في فضائك الخاصّ')) ?></li></ul><a class="et-text-link" href="<?= etE(etPoste('suivi')) ?>"><?= etE(etT('J’ai déjà un numéro de suivi', 'لديّ رقم تتبّع')) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
                    <div class="et-login-card"><span class="et-form-icon"><i class="bi bi-person" aria-hidden="true"></i></span><h3><?= etE(etT('Heureux de vous retrouver', 'مرحبًا بعودتك')) ?></h3><p><?= etE(etT('Connectez-vous à votre espace Poste.', 'سجّل الدخول إلى فضاء البريد.')) ?></p><?php $etPrefix = 'office-'; require __DIR__ . '/login.php'; ?></div>
                </div>
            <?php else: ?>
                <div class="et-section-heading"><span class="et-eyebrow"><?= etE(etT('Mon espace Poste', 'فضاء البريد الخاصّ بي')) ?></span><h2><?= etE(etT('Bonjour ', 'مرحبًا ') . $utilisateur['nom']) ?></h2><p><?= etE(etT('Choisissez la démarche que vous souhaitez effectuer.', 'اختر الإجراء الذي تريد القيام به.')) ?></p></div>
                <?php require __DIR__ . '/services-poste.php'; ?>
            <?php endif; ?>
        </div>
        <div class="et-other-service" data-office-other <?= $etIsPoste ? 'hidden' : '' ?>><span class="et-eyebrow">E-Tounsi</span><h2 data-other-title><?= etE($etService['nom'] ?? '') ?></h2><p><?= etE(etT('Cet espace présente le service. Les démarches de ce module sont prises en charge par le membre de l’équipe concerné.', 'هذا الفضاء يقدّم الخدمة. إجراءات هذه الوحدة من مسؤوليّة عضو الفريق المعنيّ.')) ?></p><a class="et-button" href="<?= etE(etHome('#services')) ?>"><?= etE(etT('Revenir aux services', 'العودة إلى الخدمات')) ?></a></div>
    </section>
</div>
