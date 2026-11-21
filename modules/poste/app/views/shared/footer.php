<footer class="et-footer" id="contact">
    <div class="et-container et-footer-main">
        <div>
            <a class="et-footer-brand" href="<?= etE(etHome()) ?>">E-Tounsi <span lang="ar">إي تونسي</span></a>
            <p><?= etE(etT('Un même accueil, cinq services publics.', 'فضاء واحد يجمع خمس خدمات عموميّة.')) ?></p>
        </div>
        <nav aria-label="<?= etE(etT('Liens utiles', 'روابط مفيدة')) ?>">
            <a href="<?= etE(etHome()) ?>"><?= etE(etT('Accueil', 'الرئيسية')) ?></a>
            <a href="<?= etE(etHome('#services')) ?>"><?= etE(etT('Nos services', 'خدماتنا')) ?></a>
            <a href="<?= etE(etHome('#comment')) ?>"><?= etE(etT('Comment ça marche', 'كيف يعمل الموقع')) ?></a>
            <a href="<?= etE(etBase() . 'index.php?page=contact&lang=' . etLang()) ?>"><?= etE(etT('Contact', 'اتصل بنا')) ?></a>
        </nav>
    </div>
    <div class="et-container et-footer-bottom">
        <small><?= etE(etT('E-Tounsi · Services publics tunisiens', 'إي تونسي · الخدمات العموميّة التونسيّة')) ?></small>
        <?php if (!defined('ETOUNSI_PORTAIL') || in_array($currentPage ?? '', ['about', 'courses', 'team', 'testimonial', '404'], true)): ?>
            <small><?= etE(etT('Template initial :', 'القالب الأصلي:')) ?> <a href="https://htmlcodex.com">HTML Codex</a></small>
        <?php else: ?>
            <small><?= etE(etT('À votre service, au quotidien.', 'على ذمّتك، كلّ يوم.')) ?></small>
        <?php endif; ?>
    </div>
</footer>
<a href="#contenu" class="et-back-top back-to-top" aria-label="<?= etE(etT('Retour en haut', 'العودة إلى الأعلى')) ?>" hidden><?= etE(etT('Haut', 'أعلى')) ?></a>
