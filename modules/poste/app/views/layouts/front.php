<!doctype html>
<html lang="<?= e($language) ?>" dir="<?= e($direction) ?>">
<head>
    <?php require_once __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
    <main id="contenu">
        <?php require_once __DIR__ . '/../' . $vue . '.php'; ?>
    </main>
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <?php require_once __DIR__ . '/../includes/scripts.php'; ?>
</body>
</html>
