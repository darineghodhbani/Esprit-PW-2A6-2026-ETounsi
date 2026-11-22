<!doctype html>
<html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head><?php require __DIR__ . '/includes/head.php'; ?></head>
<body class="page-profile page-poste">
<?php require __DIR__ . '/includes/spinner.php'; ?>
<?php require __DIR__ . '/includes/navbar.php'; ?>
<main id="main"><div class="workspace container">
<?php require __DIR__ . '/poste/menu.php'; ?>
<div class="workspace-content"><?= $content ?></div>
</div></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/scripts.php'; ?>
<script src="assets/frontoffice/js/validation.js"></script>
</body></html>
