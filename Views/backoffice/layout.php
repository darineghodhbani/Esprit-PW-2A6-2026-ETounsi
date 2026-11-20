<!DOCTYPE html>
<html lang="fr">

<head>
<?php require __DIR__ . '/partials/head.php'; ?>
</head>

<body>

<?php require __DIR__ . '/partials/sidebar.php'; ?>

  <div class="main-wrapper">

<?php require __DIR__ . '/partials/header.php'; ?>

    <?php if (!empty($flash)): ?>

      <div class="alert-custom alert-custom-<?php echo e($flash['type']); ?>"><?php echo e($flash['message']); ?></div>
    <?php endif; ?>

    <?php echo $content; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

  </div>

<?php require __DIR__ . '/partials/scripts.php'; ?>
</body>

</html>
