<footer class="container-fluid bg-dark text-light footer">
    <div class="container footer-wrap py-4">
        <div>
            <span class="footer-brand">E‑Tounsi <span lang="ar">إي تونسي</span></span>
            <p class="mb-0"><?= e(tr('Services postaux · Projet d’intégration', 'الخدمات البريدية · مشروع تطبيقي')) ?></p>
        </div>
        <div class="footer-links">
            <?php if ($utilisateur !== null && $utilisateur['role'] !== 'citoyen'): ?>
                <a href="<?= e(adminUrl()) ?>"><?= e(tr('Back Office', 'فضاء الإدارة')) ?></a>
            <?php else: ?>
                <a href="<?= e(frontUrl('profil')) ?>"><?= e(tr('Mon espace', 'فضائي')) ?></a>
            <?php endif; ?>
            <small>eLearning · <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a></small>
        </div>
    </div>
</footer>
<a href="#contenu" class="btn btn-primary back-to-top" aria-label="<?= e(tr('Retour en haut', 'العودة إلى الأعلى')) ?>" hidden><i class="bi bi-arrow-up" aria-hidden="true"></i></a>
