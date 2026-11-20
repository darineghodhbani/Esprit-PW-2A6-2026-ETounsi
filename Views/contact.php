<!doctype html>
<html lang="<?= etE(etLang()) ?>" dir="<?= etLang() === 'ar' ? 'rtl' : 'ltr' ?>">
<head><?php require __DIR__ . '/../modules/poste/app/views/shared/head.php'; ?></head>
<body class="et-portal">
    <?php require __DIR__ . '/includes/navbar.php'; ?>
    <main id="contenu" class="et-container et-section">
        <div class="et-section-heading">
            <span class="et-eyebrow">E-Tounsi · <?= etE(etT('Poste', 'البريد')) ?></span>
            <h1><?= etE($pageTitle) ?></h1>
            <p><?= etE(etT('Retrouvez les adresses des bureaux et les services disponibles pour vos envois.', 'اطّلع على عناوين المكاتب والخدمات المتاحة لإرسالياتك.')) ?></p>
        </div>
        <div class="et-postal-actions">
            <a class="et-action-card" href="<?= etE(etPoste('bureaux')) ?>">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                <h2><?= etE(etT('Trouver un bureau', 'ابحث عن مكتب')) ?></h2>
                <p><?= etE(etT('Consultez les noms, les villes et les adresses enregistrés dans l’annuaire.', 'اطّلع على أسماء المكاتب ومدنها وعناوينها المسجّلة في الدليل.')) ?></p>
                <span><?= etE(etT('Ouvrir l’annuaire', 'افتح الدليل')) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
            </a>
            <a class="et-action-card" href="<?= etE(etPoste('suivi')) ?>">
                <i class="bi bi-box-seam" aria-hidden="true"></i>
                <h2><?= etE(etT('Suivre un colis', 'تتبّع طردًا')) ?></h2>
                <p><?= etE(etT('Utilisez votre référence pour consulter l’état actuel de votre envoi.', 'استعمل رقم التتبّع للاطّلاع على الحالة الحاليّة لإرساليتك.')) ?></p>
                <span><?= etE(etT('Consulter le suivi', 'اطّلع على التتبّع')) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
            </a>
            <a class="et-action-card" href="<?= etE(etPoste('connexion')) ?>">
                <i class="bi bi-person" aria-hidden="true"></i>
                <h2><?= etE(etT('Mon espace citoyen', 'فضاء المواطن')) ?></h2>
                <p><?= etE(etT('Connectez-vous pour déclarer un envoi et retrouver vos colis.', 'سجّل الدخول للتصريح بإرسالية وللاطّلاع على طرودك.')) ?></p>
                <span><?= etE(etT('Se connecter', 'تسجيل الدخول')) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
            </a>
        </div>
    </main>
    <?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
