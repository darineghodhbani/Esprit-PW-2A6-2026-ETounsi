<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . '/partials/head.php'; ?>
<body>
    <div id="preloader"></div>

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <?php require_once __DIR__ . '/partials/header.php'; ?>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert-custom alert-custom-<?php echo $_SESSION['flash']['type']; ?>">
                <?php echo $_SESSION['flash']['message']; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php echo $content; ?>

        <?php require_once __DIR__ . '/partials/footer.php'; ?>
    </div>

    <?php require_once __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
