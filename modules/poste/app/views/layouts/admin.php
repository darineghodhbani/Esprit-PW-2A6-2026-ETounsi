<!doctype html>
<html lang="fr">
<?php require __DIR__ . '/../partials/head.php'; ?>
<body>
    <a class="skip-link" href="#contenu">Aller au contenu</a>
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="main-wrapper">
        <?php require __DIR__ . '/../partials/header.php'; ?>
        <main id="contenu" class="poste-main" tabindex="-1">
            <?php if ($flash !== null): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
            <?php endif; ?>
            <?= $content ?>
        </main>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
    </div>
    <?php require __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
