<?php $etIsPoste = ($etServiceKey ?? 'poste') === 'poste'; ?>
<noscript><style>.et-office-entry.et-office-entry--photo{display:none}.et-office-welcome{opacity:1;transform:none}</style></noscript>
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
        <div class="et-office-entry et-office-entry--photo">
            <svg width="0" height="0" class="et-door-definitions" aria-hidden="true">
                <defs>
                    <clipPath id="et-door-opening" clipPathUnits="userSpaceOnUse"><path d="M207 1468V454C287 370 382 327 517 321C641 323 746 368 825 453V1468Z"/></clipPath>
                    <clipPath id="et-door-left-clip" clipPathUnits="userSpaceOnUse"><path d="M207 1468V454C287 370 382 327 517 321V1468Z"/></clipPath>
                    <clipPath id="et-door-right-clip" clipPathUnits="userSpaceOnUse"><path d="M517 321C641 323 746 368 825 453V1468H517Z"/></clipPath>
                    <mask id="et-door-stone-mask" maskUnits="userSpaceOnUse" x="0" y="0" width="957" height="1644"><rect width="957" height="1644" fill="white"/><path d="M207 1468V454C287 370 382 327 517 321C641 323 746 368 825 453V1468Z" fill="black"/></mask>
                </defs>
            </svg>
            <span class="et-door-stage" aria-hidden="true">
                <span class="et-door-artboard"><span class="et-door-camera">
                    <img class="et-door-photo" data-door-interior src="<?= etE(etAsset('images/portal/' . ($etService['image'] ?? 'bureau-poste') . '.webp')) ?>" alt="" draggable="false">
                    <span class="et-door-interior-shade"></span>
                    <span class="et-door-space">
                        <span class="et-door-left"><img class="et-door-leaf-photo et-door-clip-left" src="<?= etE(etAsset('images/portal/portes/porte-fermee.webp')) ?>" alt="" draggable="false"><span class="et-door-leaf-shade et-door-clip-left" data-door-shade-left></span><span class="et-door-edge et-door-edge-left"></span></span>
                        <span class="et-door-right"><img class="et-door-leaf-photo et-door-clip-right" src="<?= etE(etAsset('images/portal/portes/porte-fermee.webp')) ?>" alt="" draggable="false"><span class="et-door-leaf-shade et-door-clip-right" data-door-shade-right></span><span class="et-door-edge et-door-edge-right"></span></span>
                    </span>
                    <svg class="et-door-frame" viewBox="0 0 957 1644"><image href="<?= etE(etAsset('images/portal/portes/porte-fermee.webp')) ?>" width="957" height="1644" mask="url(#et-door-stone-mask)"/></svg>
                    <span class="et-door-plaque" data-door-label><?= etE($etService['nom'] ?? etT('Poste', 'البريد')) ?></span>
                </span></span>
            </span>
            <button type="button" class="et-door-skip" data-door-skip><?= etE(etT('Passer l’animation', 'تجاوز الحركة')) ?></button>
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
