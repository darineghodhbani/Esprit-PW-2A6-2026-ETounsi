<footer class="et-footer" id="contact">
    <div class="et-container et-footer-main">
        <div>
            <a class="et-footer-brand" href="<?= etE(etHome()) ?>">E-Tounsi <span lang="ar">إي تونسي</span></a>
            <p><?= etE(etT('Les services publics, plus proches de vous.', 'الخدمات العموميّة، أقرب ليك.')) ?></p>
        </div>
        <nav aria-label="<?= etE(etT('Liens utiles', 'روابط مفيدة')) ?>">
            <a href="<?= etE(etHome('#services')) ?>"><?= etE(etT('Les cinq services', 'الخدمات الخمس')) ?></a>
            <a href="<?= etE(etPoste('bureaux')) ?>"><?= etE(etT('Trouver un bureau de poste', 'ابحث عن مكتب بريد')) ?></a>
            <a href="<?= etE(etBase()) ?>index.php?page=team"><?= etE(etT('Notre équipe', 'فريقنا')) ?></a>
            <a href="<?= etE(etPoste('profil')) ?>"><?= etE(etT('Mon compte', 'حسابي')) ?></a>
        </nav>
    </div>
    <div class="et-container et-footer-bottom">
        <small><?= etE(etT('Projet Technologies Web · Power Girls', 'مشروع تقنيات الويب · Power Girls')) ?> — <?= etE(etT('Module Poste : Maryem Kochbati', 'وحدة البريد: Maryem Kochbati')) ?></small>
        <small><?= etE(etT('Template initial eLearning :', 'قالب eLearning الأصلي:')) ?> <a href="https://htmlcodex.com">HTML Codex</a></small>
    </div>
</footer>
<a href="#contenu" class="et-back-top back-to-top" aria-label="<?= etE(etT('Retour en haut', 'العودة إلى الأعلى')) ?>" hidden><i class="bi bi-arrow-up" aria-hidden="true"></i></a>
