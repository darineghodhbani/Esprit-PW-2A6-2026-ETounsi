<?php

$sceneModules = array_map(static fn(array $m): array => [
    'id'            => $m['id'],
    'label'         => m($m, 'label'),
    'welcome'       => m($m, 'welcome'),
    'cta'           => m($m, 'cta'),
    'publicPath'    => 'index.php?page=module&id=' . $m['id'],
    'interior'      => $m['interior'],
    'hotspot'       => $m['hotspot'],
    'threshold'     => $m['threshold'],
    'approachScale' => $m['approachScale'],
], array_values($modules));

$sceneI18n = [
    'loading' => t('scene.loading'),
    'error'   => t('scene.error'),
];

$jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
           | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;
?>
<!DOCTYPE html>
<html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>
<body class="page-home">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main id="main">

        <section class="portal" data-portal data-locale="<?= et_lang() ?>">
            <div class="hero-intro" data-scene-intro>
                <h1><?= e(t('hero.first')) ?> <span><?= e(t('hero.second')) ?></span></h1>
            </div>

            <div class="street-frame">
                <div data-scene-stage>
                    <div data-street-plane>
                        <picture>
                            <source media="(max-width:700px)"
                                    srcset="assets/frontoffice/img/street/street-mobile.webp">
                            <img src="assets/frontoffice/img/street/street-desktop.webp"
                                 width="1678" height="937" fetchpriority="high"
                                 alt="<?= e(t('scene.alt')) ?>">
                        </picture>
                        <?php foreach ($modules as $item): ?>
                            <a class="hotspot" data-hotspot="<?= e($item['id']) ?>"
                               href="index.php?page=module&id=<?= e($item['id']) ?>"
                               style="--u:<?= $item['hotspot']['u'] ?>;--v:<?= $item['hotspot']['v'] ?>"
                               aria-label="<?= e(m($item, 'label')) ?>">
                                <span class="hotspot-label">
                                    <?= e(m($item, 'label')) ?><?= et_icon('arrow-up-right') ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div data-camera-viewport>
                        <img data-interior hidden width="1672" height="941" alt="">
                        <div data-welcome hidden>
                            <span class="welcome-eyebrow">E-TOUNSI</span>
                            <h2 data-welcome-title></h2>
                            <a data-handoff href="index.php?page=home">
                                <span data-handoff-label></span><?= et_icon('arrow-right') ?>
                            </a>
                        </div>
                        <div data-portal-controls hidden>
                            <button type="button" data-return>
                                <?= et_icon('arrow-left') ?><?= e(t('scene.return')) ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <p class="scene-status" data-scene-status role="status" aria-live="polite"></p>

            <a class="portal-scroll" href="#services" data-scroll-services>
                <span><?= e(t('hero.scroll')) ?></span>
                <span class="scroll-line" aria-hidden="true"></span>
            </a>

            <script type="application/json" data-administrations><?= json_encode($sceneModules, $jsonFlags) ?></script>
            <script type="application/json" data-scene-i18n><?= json_encode($sceneI18n, $jsonFlags) ?></script>
        </section>

        <section class="services-section container" id="services">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><?= e(t('section.eyebrow')) ?></p>
                    <h2 id="services-title" tabindex="-1"><?= e(t('section.title')) ?></h2>
                    <p><?= e(t('section.description')) ?></p>
                </div>
                <a class="text-link" href="index.php?page=administrations">
                    <?= e(t('section.all')) ?><?= et_icon('arrow-up-right') ?>
                </a>
            </div>
            <div class="services-grid">
                <?php foreach ($modules as $item) { require __DIR__ . '/includes/service-card.php'; } ?>
            </div>
        </section>

        <section class="trust-section container">
            <div class="trust-intro">
                <img class="brand-logo" src="assets/frontoffice/img/brand/e-tounsi-mark.svg" width="34" height="43" alt="">
                <h2><?= e(t('trust.title')) ?></h2>
            </div>
            <div class="trust-grid">
                <div>
                    <?= et_icon('user') ?>
                    <h3><?= e(t('trust.one')) ?></h3>
                    <p><?= e(t('trust.one.text')) ?></p>
                </div>
                <div>
                    <?= et_icon('sparkles') ?>
                    <h3><?= e(t('trust.two')) ?></h3>
                    <p><?= e(t('trust.two.text')) ?></p>
                </div>
                <div>
                    <?= et_icon('shield-check') ?>
                    <h3><?= e(t('trust.three')) ?></h3>
                    <p><?= e(t('trust.three.text')) ?></p>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
