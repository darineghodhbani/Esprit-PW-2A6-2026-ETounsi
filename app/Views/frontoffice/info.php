<!DOCTYPE html>
<html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-info">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">
        <section class="container page-section prose-page">
            <?php if ($info === 'aide'): ?>
                <h1><?= e(t('info.help.title')) ?></h1>
                <p class="page-lead"><?= e(t('info.help.text')) ?></p>

                <div class="faq-list">
                    <details>
                        <summary><?= e(t('faq.one.q')) ?><?= et_icon('chevron-down') ?></summary>
                        <p><?= e(t('faq.one.a')) ?></p>
                    </details>
                    <details>
                        <summary><?= e(t('faq.two.q')) ?><?= et_icon('chevron-down') ?></summary>
                        <p><?= e(t('faq.two.a')) ?></p>
                    </details>
                    <details>
                        <summary><?= e(t('faq.three.q')) ?><?= et_icon('chevron-down') ?></summary>
                        <p><?= e(t('faq.three.a')) ?></p>
                    </details>
                </div>

            <?php elseif ($info === 'projet'): ?>
                <h1><?= e(t('info.project.title')) ?></h1>
                <p class="page-lead"><?= e(t('info.project.text')) ?></p>
                <p class="page-lead"><?= e(t('info.project.team')) ?></p>
                <a class="button button-dark" href="index.php?page=administrations">
                    <?= e(t('nav.services')) . et_icon('arrow-right') ?>
                </a>

            <?php elseif ($info === 'confidentialite'): ?>
                <h1><?= e(t('info.privacy.title')) ?></h1>
                <p class="page-lead"><?= e(t('info.privacy.text')) ?></p>
                <p class="page-lead"><?= e(t('info.privacy.roles')) ?></p>
            <?php endif; ?>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
