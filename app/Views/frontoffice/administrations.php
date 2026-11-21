<!DOCTYPE html>
<html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-administrations">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">
        <section class="container page-section">
            <p class="eyebrow"><?= e(t('section.eyebrow')) ?></p>
            <h1><?= e(t('catalog.title')) ?></h1>
            <p class="page-lead"><?= e(t('catalog.description')) ?></p>

            <div class="services-grid catalogue-grid">
                <?php foreach ($modules as $item) { require __DIR__ . '/includes/service-card.php'; } ?>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
