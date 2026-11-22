<?php

require_once __DIR__ . '/icons.php';

$estConnecte = !empty($_SESSION['id_citoyen']);
$estStaff    = $estConnecte && in_array($_SESSION['role'], ['agent', 'admin'], true);
$lienCompte  = $estConnecte ? ($estStaff ? 'dashboard' : 'profile') : 'login';
$autreLangue = et_lang() === 'fr' ? 'ar' : 'fr';
?>
<a class="skip-link" href="#main"><?= e(t('nav.skip')) ?></a>
<header class="site-header">
    <a class="wordmark" dir="ltr" href="index.php?page=home" aria-label="E-Tounsi — <?= e(t('nav.home')) ?>">
        <img class="brand-logo" src="assets/frontoffice/img/brand/e-tounsi-mark.svg" width="34" height="43" alt="">
        <span>E-Tounsi</span>
    </a>

    <nav class="main-nav" aria-label="<?= e(t('nav.primary')) ?>" id="main-nav" data-collapsed="true">
        <a href="index.php?page=home"
           <?= $currentPage === 'home' ? 'aria-current="page"' : '' ?>><?= e(t('nav.home')) ?></a>
        <a href="index.php?page=administrations"
           <?= $currentPage === 'administrations' ? 'aria-current="page"' : '' ?>><?= e(t('nav.services')) ?></a>
        <a href="index.php?page=aide"
           <?= $currentPage === 'aide' ? 'aria-current="page"' : '' ?>><?= e(t('nav.help')) ?></a>
    </nav>

    <div class="header-actions">
        <a class="language-switch"
           href="index.php?page=lang&to=<?= $autreLangue ?>"
           lang="<?= $autreLangue ?>" hreflang="<?= $autreLangue ?>"
           aria-label="<?= e(t('nav.language')) ?>">
            <?= et_icon('globe') ?>
            <span><?= et_lang() === 'fr' ? 'العربية' : 'FR' ?></span>
        </a>
        <?php if ($estConnecte): ?>
            <a class="button button-small button-dark account-button"
               href="index.php?page=<?= $lienCompte ?>" aria-label="<?= e(t($estStaff ? 'nav.backoffice' : 'nav.account')) ?>">
                <?= et_icon('user') ?>
                <span><?= e(t($estStaff ? 'nav.backoffice' : 'nav.account')) ?></span>
            </a>
            <form method="post" action="index.php?page=logout" class="logout-inline"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><button type="submit" class="icon-button"
               aria-label="<?= e(t('nav.logout')) ?>" title="<?= e(t('nav.logout')) ?>">
                <?= et_icon('log-out') ?>
            </button></form>
        <?php else: ?>
            <a class="button button-small button-dark account-button"
               href="index.php?page=login"
               aria-label="<?= e(t('nav.login')) ?>"
               <?= $currentPage === 'login' ? 'aria-current="page"' : '' ?>>
                <?= et_icon('user') ?>
                <span><?= e(t('nav.login')) ?></span>
            </a>
        <?php endif; ?>
        <button class="icon-button mobile-menu" type="button" aria-expanded="false"
                aria-controls="main-nav" aria-label="<?= e(t('nav.menu')) ?>" data-menu-toggle>
            <?= et_icon('menu') ?>
        </button>
    </div>
</header>
