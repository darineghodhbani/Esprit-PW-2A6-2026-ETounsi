<!doctype html>
<html lang="<?= et_lang() ?>" dir="<?= et_dir() ?>">
<head><?php require __DIR__ . '/includes/head.php'; ?></head>
<body class="page-profile page-poste">
<?php require __DIR__ . '/includes/spinner.php'; ?>
<?php require __DIR__ . '/includes/navbar.php'; ?>
<main id="main"><div class="workspace container">
<?php if (in_array($currentPage, ['mon-dossier', 'mes-beneficiaires', 'beneficiaire-form', 'mes-remboursements', 'remboursement-form', 'cnam-refuse'], true)): ?>
<?php require __DIR__ . '/cnam/menu.php'; ?>
<?php elseif (in_array($currentPage, ['lignes-transport', 'stations-transport'], true)): ?>
<?php require __DIR__ . '/transport/menu.php'; ?>
<?php else: ?>
<?php require __DIR__ . '/poste/menu.php'; ?>
<?php endif; ?>
<div class="workspace-content"><?= $content ?></div>
</div></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/scripts.php'; ?>
<script src="assets/frontoffice/js/validation.js"></script>
</body></html>
