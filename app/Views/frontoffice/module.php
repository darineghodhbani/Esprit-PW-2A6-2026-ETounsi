<!DOCTYPE html>
<html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-module">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">
        <section class="module-page container page-section"
                 data-module="<?= e($module['id']) ?>"
                 style="--module-accent: <?= e($module['accent']) ?>">
            <a class="breadcrumb" href="index.php?page=administrations">
                <?= et_icon('arrow-left') . e(t('nav.services')) ?>
            </a>

            <img class="module-hero-img" src="<?= e($module['interior']) ?>"
                 width="1672" height="941" alt="" loading="lazy">

            <div class="module-heading">
                <div>
                    <p class="eyebrow"><?= e(t('module.eyebrow')) ?></p>
                    <?php if ($module['ready']): ?>
                        <span class="badge badge-success"><?= e(t('module.status.ready')) ?></span>
                    <?php else: ?>
                        <span class="badge badge-neutral"><?= e(t('module.status')) ?></span>
                    <?php endif; ?>
                    <h1 id="module-title" data-module-title tabindex="-1"><?= e(m($module, 'label')) ?></h1>
                    <p class="page-lead"><?= e(m($module, 'description')) ?></p>
                    <?php if ($module['ready']): ?>
                        <a class="button button-dark" href="index.php?page=<?= e($module['route']) ?>">
                            <?= e(t('module.access')) . et_icon('arrow-right') ?>
                        </a>
                    <?php else: ?>
                        <button class="button button-dark" type="button" disabled aria-disabled="true">
                            <?= e(t('module.access')) . et_icon('arrow-right') ?>
                        </button>
                    <?php endif; ?>
                </div>
                <span class="module-hero-icon"><?= et_icon($module['icon']) ?></span>
            </div>

            <?php if (!$module['ready']): ?>
                <div class="alert alert-info">
                    <?= et_icon('info') ?>
                    <p><?= e(t('module.notice')) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($module['ready']): ?>
                <h2><?= e(t('module.services.ready')) ?></h2>
                <div class="planned-services">
                    <?php foreach ($module['services'] as $i => $serviceKey): ?>
                        <a class="service-link" href="index.php?page=<?= e($module['pages'][$i]) ?>">
                            <article>
                                <span class="step-number">0<?= $i + 1 ?></span>
                                <div>
                                    <h3><?= e(t($serviceKey)) ?></h3>
                                    <span class="badge badge-success"><?= e(t('card.available')) ?></span>
                                </div>
                                <span class="card-arrow"><?= et_icon('arrow-up-right') ?></span>
                            </article>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2><?= e(t('module.services')) ?></h2>
                <div class="planned-services">
                    <?php foreach ($module['services'] as $i => $serviceKey): ?>
                        <article>
                            <span class="step-number">0<?= $i + 1 ?></span>
                            <div>
                                <h3><?= e(t($serviceKey)) ?></h3>
                                <span class="badge badge-neutral"><?= e(t('card.planned')) ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="module-bottom">
                <a class="text-link" href="index.php?page=administrations">
                    <?= e(t('module.other')) . et_icon('arrow-up-right') ?>
                </a>
                <a class="text-link" href="index.php?page=login">
                    <?= e(t('module.agent')) . et_icon('arrow-up-right') ?>
                </a>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
