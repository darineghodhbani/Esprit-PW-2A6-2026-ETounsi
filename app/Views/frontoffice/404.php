<!DOCTYPE html>
<html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-404">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">
        <section class="container page-section error-page">
            <p class="error-code">404</p>
            <h1><?= e(t('error.title')) ?></h1>
            <p class="page-lead"><?= e(t('catalog.clear')) ?></p>
            <a class="button button-dark" href="index.php?page=home">
                <?= e(t('common.back')) . et_icon('arrow-right') ?>
            </a>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
