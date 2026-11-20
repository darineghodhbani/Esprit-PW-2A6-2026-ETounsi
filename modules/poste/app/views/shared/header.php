<?php
$etConnected = $utilisateur ?? null;
$etModule = !defined('ETOUNSI_PORTAIL');
$etOther = etLang() === 'fr' ? 'ar' : 'fr';
$etLanguageParams = $_GET;
unset($etLanguageParams['csrf']);
$etLanguageParams['lang'] = $etOther;
?>
<a class="et-skip" href="#contenu"><?= etE(etT('Aller au contenu', 'انتقل إلى المحتوى')) ?></a>
<header class="et-header">
    <div class="et-container et-header-inner">
        <a class="et-brand" href="<?= etE(etHome()) ?>" aria-label="<?= etE(etT('E-Tounsi — Accueil', 'إي تونسي — الرئيسية')) ?>">
            <img src="<?= etE(etAsset('images/logo-etounsi.png')) ?>" width="39" height="54" alt="">
            <span>E-Tounsi<small lang="ar" dir="rtl">إي تونسي</small></span>
        </a>
        <button class="et-menu-toggle" type="button" data-menu-toggle aria-controls="et-navigation" aria-expanded="false" aria-label="<?= etE(etT('Ouvrir le menu', 'افتح القائمة')) ?>"><i class="bi bi-list" aria-hidden="true"></i></button>
        <nav class="et-navigation" id="et-navigation" aria-label="<?= etE(etT('Navigation principale', 'التنقّل الرئيسي')) ?>">
            <a href="<?= etE(etHome()) ?>"><?= etE(etT('Accueil', 'الرئيسية')) ?></a>
            <?php if ($etModule): ?>
                <a href="<?= etE(etPoste()) ?>"><?= etE(etT('Services postaux', 'الخدمات البريدية')) ?></a>
                <a href="<?= etE(etPoste('suivi')) ?>"><?= etE(etT('Suivre un colis', 'تتبّع طرد')) ?></a>
                <a href="<?= etE(etPoste('bureaux')) ?>"><?= etE(etT('Bureaux', 'المكاتب')) ?></a>
            <?php else: ?>
                <a href="<?= etE(etHome('#services')) ?>"><?= etE(etT('Nos services', 'خدماتنا')) ?></a>
                <a href="<?= etE(etHome('#comment')) ?>"><?= etE(etT('Comment ça marche', 'كيف يعمل الموقع')) ?></a>
                <a href="<?= etE(etHome('#contact')) ?>"><?= etE(etT('Contact', 'اتصل بنا')) ?></a>
            <?php endif; ?>
        </nav>
        <div class="et-header-actions">
            <a class="et-language" href="?<?= etE(http_build_query($etLanguageParams)) ?>" lang="<?= etE($etOther) ?>"><?= $etOther === 'ar' ? 'العربية' : 'FR' ?></a>
            <?php if ($etConnected === null): ?>
                <a class="et-button et-button-small" href="<?= etE(etPoste('connexion')) ?>"><i class="bi bi-person" aria-hidden="true"></i><span><?= etE(etT('Se connecter', 'تسجيل الدخول')) ?></span></a>
            <?php else: ?>
                <details class="et-account-menu">
                    <summary class="et-button et-button-small"><i class="bi bi-person" aria-hidden="true"></i><span><?= etE(etT('Mon espace', 'فضائي')) ?></span><i class="bi bi-chevron-down" aria-hidden="true"></i></summary>
                    <div>
                        <strong><?= etE($etConnected['nom']) ?></strong>
                        <a href="<?= etE($etConnected['role'] === 'admin' ? etPoste('dashboard', true) : etPoste('historique')) ?>"><?= etE($etConnected['role'] === 'admin' ? etT('Administration', 'الإدارة') : etT('Mes colis', 'طرودي')) ?></a>
                        <a href="<?= etE(etPoste('profil')) ?>"><?= etE(etT('Mon profil', 'ملفّي')) ?></a>
                        <form method="post" action="<?= etE(etPoste('deconnexion')) ?>" novalidate><input type="hidden" name="csrf" value="<?= etE($jeton) ?>"><button type="submit"><?= etE(etT('Déconnexion', 'خروج')) ?></button></form>
                    </div>
                </details>
            <?php endif; ?>
        </div>
    </div>
</header>
