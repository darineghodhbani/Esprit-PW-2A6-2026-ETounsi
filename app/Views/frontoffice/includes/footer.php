<footer class="site-footer container">
    <div class="footer-top">
        <div>
            <a class="wordmark" dir="ltr" href="index.php?page=home">
                <img class="brand-logo" src="assets/frontoffice/img/brand/e-tounsi-mark.svg" width="34" height="43" alt="">
                <span>E-Tounsi</span>
            </a>
            <p><?= e(t('footer.description')) ?></p>
        </div>
        <nav aria-label="<?= e(t('nav.footer')) ?>">
            <a href="index.php?page=projet"><?= e(t('footer.project')) ?></a>
            <a href="index.php?page=aide"><?= e(t('nav.help')) ?></a>
            <a href="index.php?page=confidentialite"><?= e(t('footer.privacy')) ?></a>
            <a href="index.php?page=administrations"><?= e(t('nav.services')) ?></a>
        </nav>
    </div>
    <div class="footer-bottom">
        <span>
            Tunisie
            <span class="footer-separator" aria-hidden="true">·</span>
            تونس
        </span>
        <span>E-Tounsi — Esprit School of Engineering · <?= date('Y') ?></span>
    </div>
</footer>

<button id="backToTop" title="<?= e(t('a11y.backtop')) ?>" aria-label="<?= e(t('a11y.backtop')) ?>">&uarr;</button>
