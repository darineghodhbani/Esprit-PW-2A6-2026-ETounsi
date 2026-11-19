<?php
$office = $data['office'];
$fields = $data['fields'];
$errors = $data['errors'];
?>
<a class="poste-back" href="?page=bureaux"><i class="bi bi-arrow-left" aria-hidden="true"></i> Bureaux de poste</a>
<div class="page-header poste-page-header">
    <h1 class="page-title"><?= e($title) ?></h1>
</div>
<section class="poste-form-card" aria-label="Coordonnées du bureau">
    <p class="poste-form-intro">Tous les champs sont obligatoires.</p>
    <?php require __DIR__ . '/../partials/form-errors.php'; ?>
    <form action="?page=bureau-save" method="post" novalidate data-poste-save>
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <?php if (!empty($office['id_bureau'])): ?><input type="hidden" name="id" value="<?= e($office['id_bureau']) ?>"><?php endif; ?>
        <?php foreach ($fields as $field => $info): ?>
            <div class="poste-field">
                <label for="<?= e($field) ?>"><?= e($info['label']) ?></label>
                <input class="form-control-custom<?= isset($errors[$field]) ? ' is-invalid' : '' ?>" id="<?= e($field) ?>"
                       type="text" required name="<?= e($field) ?>" value="<?= e($office[$field]) ?>" maxlength="<?= e($info['limit']) ?>"
                       <?php if (isset($errors[$field])): ?>aria-invalid="true" aria-describedby="erreur-<?= e($field) ?>"<?php endif; ?>>
                <?php if (isset($errors[$field])): ?><p class="invalid-feedback" id="erreur-<?= e($field) ?>"><?= e($errors[$field]) ?></p><?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="poste-table-actions">
            <button class="poste-btn poste-btn-primary" type="submit">Enregistrer</button>
            <a class="poste-btn poste-btn-quiet" href="?page=bureaux">Annuler</a>
        </div>
    </form>
</section>
