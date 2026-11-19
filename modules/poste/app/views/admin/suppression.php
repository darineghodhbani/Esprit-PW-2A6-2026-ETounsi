<?php
$id = $data['id'];
$reference = $data['reference'];
$action = $data['action'];
$message = $data['message'];
?>
<section class="poste-form-card poste-delete-card" aria-labelledby="suppression">
    <i class="bi bi-exclamation-triangle poste-delete-icon" aria-hidden="true"></i>
    <h1 class="page-title" id="suppression"><?= e($title) ?></h1>
    <p><strong><?= e($reference) ?></strong></p>
    <p class="poste-form-intro"><?= e($message) ?></p>
    <?php if (!empty($data['errors'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($data['errors'] as $messageErreur): ?><p class="mb-0"><?= e($messageErreur) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="?espace=admin&amp;page=<?= e($action) ?>" method="post" class="poste-table-actions">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="id" value="<?= e($id) ?>">
        <a class="poste-btn poste-btn-outline" href="?espace=admin&amp;page=<?= e($section) ?>">Annuler</a>
        <button class="poste-btn poste-btn-primary" type="submit">Confirmer la suppression</button>
    </form>
</section>
