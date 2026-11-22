<?php
$id = $old['id'];
$reference = $old['reference'];
$action = $old['action'];
$message = $old['message'];
?>
<section class="poste-form-card poste-delete-card" aria-labelledby="suppression">
    <i class="bi bi-exclamation-triangle poste-delete-icon" aria-hidden="true"></i>
    <h1 class="page-title" id="suppression"><?php echo e($title); ?></h1>
    <p><strong><?php echo e($reference); ?></strong></p>
    <p class="poste-form-intro"><?php echo e($message); ?></p>
    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($erreurs as $messageErreur): ?><p class="mb-0"><?php echo e($messageErreur); ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="?espace=admin&amp;page=<?php echo e($action); ?>" method="post" class="poste-table-actions" novalidate>
        <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
        <input type="hidden" name="id" value="<?php echo e($id); ?>">
        <a class="btn-custom btn-custom-light" href="?espace=admin&amp;page=<?php echo e($section); ?>">Annuler</a>
        <button class="btn-custom btn-custom-primary" type="submit">Confirmer la suppression</button>
    </form>
</section>
