<!doctype html><html lang="<?= etE($language) ?>" dir="<?= $language === 'ar' ? 'rtl' : 'ltr' ?>"><head><?php require __DIR__ . '/../modules/poste/app/views/shared/head.php'; ?></head><body class="et-portal">
<?php require __DIR__ . '/../modules/poste/app/views/shared/header.php'; ?>
<main id="contenu"><?php if (isset($etService)): require __DIR__ . '/../modules/poste/app/views/shared/office.php'; else: ?><div class="et-container et-section"><h1><?= etE($pageTitle) ?></h1><a class="et-button" href="<?= etE(etHome('#services')) ?>"><?= etE(etT('Revenir aux services', 'العودة إلى الخدمات')) ?></a></div><?php endif; ?></main>
<?php require __DIR__ . '/../modules/poste/app/views/shared/footer.php'; ?>
</body></html>
