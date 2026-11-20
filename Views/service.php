<?php
$language = etLang();
$etModules = etServices();
$etServiceKey = is_string($_GET['service'] ?? null) ? $_GET['service'] : 'poste';
if (!isset($etModules[$etServiceKey])) {
    http_response_code(404);
    $pageTitle = etT('Service introuvable', 'الخدمة غير موجودة');
} else {
    $etService = $etModules[$etServiceKey];
    $pageTitle = $etService['nom'];
}
$utilisateur = utilisateurConnecte();
$jeton = csrfToken();
$etOfficePage = true;
?>
<!doctype html><html lang="<?= etE($language) ?>" dir="<?= $language === 'ar' ? 'rtl' : 'ltr' ?>"><head><?php require __DIR__ . '/../modules/poste/app/views/shared/head.php'; ?></head><body class="et-portal">
<?php require __DIR__ . '/../modules/poste/app/views/shared/header.php'; ?>
<main id="contenu"><?php if (isset($etService)): require __DIR__ . '/../modules/poste/app/views/shared/office.php'; else: ?><div class="et-container et-section"><h1><?= etE($pageTitle) ?></h1><a class="et-button" href="<?= etE(etHome('#services')) ?>"><?= etE(etT('Revenir aux services', 'العودة إلى الخدمات')) ?></a></div><?php endif; ?></main>
<?php require __DIR__ . '/../modules/poste/app/views/shared/footer.php'; ?>
</body></html>
