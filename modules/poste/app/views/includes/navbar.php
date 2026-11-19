<?php
$liens = [
    'accueil' => tr('Accueil', 'الرئيسية'),
    'suivi' => tr('Suivre un colis', 'تتبّع طرد'),
    'bureaux' => tr('Bureaux de poste', 'مكاتب البريد'),
];
if ($utilisateur === null || $utilisateur['role'] === 'citoyen') {
    $liens['historique'] = tr('Mes colis', 'طرودي');
    $liens['envoi'] = tr('Déclarer un envoi', 'التصريح بالإرسال');
}

$autreLangue = 'ar';
$libelleLangue = 'العربية';
if ($language === 'ar') {
    $autreLangue = 'fr';
    $libelleLangue = 'Français';
}
?>
<a class="skip-link visually-hidden-focusable position-absolute top-0 btn btn-primary" href="#contenu"><?= e(tr('Aller au contenu', 'انتقل إلى المحتوى')) ?></a>
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top border-bottom" aria-label="<?= e(tr('Navigation principale', 'التنقّل الرئيسي')) ?>">
    <div class="container poste-nav">
        <a href="<?= e(frontUrl()) ?>" class="navbar-brand d-inline-flex align-items-center gap-2">
            <img class="logo-etounsi" src="public/assets/images/logo-etounsi.png" alt="E‑Tounsi · إي تونسي" height="54">
            <span class="libelle-poste"><?= e(tr('Services postaux', 'الخدمات البريدية')) ?></span>
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="<?= e(tr('Ouvrir le menu', 'افتح القائمة')) ?>">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav">
                <?php foreach ($liens as $page => $libelle): ?>
                    <?php $estActive = $currentPage === $page; ?>
                    <a class="nav-item nav-link<?= $estActive ? ' active' : '' ?>"
                        href="<?= e(frontUrl($page)) ?>" <?php if ($estActive) echo 'aria-current="page"'; ?>>
                        <?= e($libelle) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="compte-navigation">
                <?php if ($utilisateur === null): ?>
                    <a class="nav-link" href="<?= e(frontUrl('connexion')) ?>"><?= e(tr('Se connecter', 'تسجيل الدخول')) ?></a>
                <?php else: ?>
                    <a class="nav-link compte-nom" href="<?= e(frontUrl('profil')) ?>" title="<?= e(tr('Mon profil', 'ملفّي') . ' · ' . $utilisateur['nom']) ?>"><?= e($utilisateur['nom']) ?></a>
                    <?php if ($utilisateur['role'] !== 'citoyen'): ?>
                        <a class="nav-link" href="<?= e(adminUrl()) ?>"><?= e(tr('Administration', 'الإدارة')) ?></a>
                    <?php endif; ?>
                    <form method="post" action="<?= e(frontUrl('deconnexion')) ?>">
                        <input type="hidden" name="csrf" value="<?= e($jeton) ?>">
                        <button type="submit" class="nav-link deconnexion-btn"><?= e(tr('Déconnexion', 'خروج')) ?></button>
                    </form>
                <?php endif; ?>
                <a class="btn btn-outline-primary language-switch"
                    href="<?= e(frontUrl($currentPage, $parametresLangue, $autreLangue)) ?>"
                    lang="<?= e($autreLangue) ?>"><?= e($libelleLangue) ?></a>
            </div>
        </div>
    </div>
</nav>
