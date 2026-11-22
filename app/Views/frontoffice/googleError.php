<!doctype html><html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head><?php require __DIR__ . '/includes/head.php'; ?></head><body class="page-login">
<?php require __DIR__ . '/includes/navbar.php'; ?>
<main id="main" class="container page-section"><h1><?= e(tr('Connexion Google','الدخول بحساب Google')) ?></h1>
<p class="alert alert-info" role="alert"><?= e($message) ?></p>
<a class="button button-dark" href="index.php?page=login"><?= e(tr('Revenir à la connexion','الرجوع إلى تسجيل الدخول')) ?></a></main>
<?php require __DIR__ . '/includes/footer.php'; require __DIR__ . '/includes/scripts.php'; ?></body></html>
