<?php $modification = !empty($old['id_bureau']); ?>
<a class="poste-back" href="index.php?page=admin-bureaux">Bureaux de poste</a>
<div class="page-header poste-page-header"><h1 class="page-title"><?php echo e($title); ?></h1></div>
<section class="poste-form-card" aria-label="Coordonnées du bureau">
    <p class="poste-form-intro">Tous les champs sont obligatoires.</p>
    <?php require __DIR__ . '/../../partials/poste-errors.php'; ?>
    <form action="?espace=admin&amp;page=<?php echo $modification ? 'updateBureau' : 'addBureau'; ?>" method="post" novalidate data-poste-save>
        <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
        <?php if ($modification): ?><input type="hidden" name="id" value="<?php echo e($old['id_bureau']); ?>"><?php endif; ?>
        <div class="poste-field">
            <label for="nom">Nom du bureau</label>
            <input class="form-control-custom<?php echo isset($erreurs['nom']) ? ' is-invalid' : ''; ?>"
                   id="nom" name="nom" type="text" required maxlength="100" value="<?php echo e($old['nom']); ?>"
                   <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
            <?php if (isset($erreurs['nom'])): ?><p class="invalid-feedback" id="erreur-nom"><?php echo e($erreurs['nom']); ?></p><?php endif; ?>
        </div>
        <div class="poste-field">
            <label for="adresse">Adresse</label>
            <input class="form-control-custom<?php echo isset($erreurs['adresse']) ? ' is-invalid' : ''; ?>"
                   id="adresse" name="adresse" type="text" required maxlength="200" value="<?php echo e($old['adresse']); ?>"
                   <?php if (isset($erreurs['adresse'])): ?>aria-invalid="true" aria-describedby="erreur-adresse"<?php endif; ?>>
            <?php if (isset($erreurs['adresse'])): ?><p class="invalid-feedback" id="erreur-adresse"><?php echo e($erreurs['adresse']); ?></p><?php endif; ?>
        </div>
        <div class="poste-field">
            <label for="ville">Ville</label>
            <input class="form-control-custom<?php echo isset($erreurs['ville']) ? ' is-invalid' : ''; ?>"
                   id="ville" name="ville" type="text" required maxlength="80" value="<?php echo e($old['ville']); ?>"
                   <?php if (isset($erreurs['ville'])): ?>aria-invalid="true" aria-describedby="erreur-ville"<?php endif; ?>>
            <?php if (isset($erreurs['ville'])): ?><p class="invalid-feedback" id="erreur-ville"><?php echo e($erreurs['ville']); ?></p><?php endif; ?>
        </div>
        <div class="poste-table-actions">
            <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
            <a class="btn-custom btn-custom-light" href="index.php?page=admin-bureaux">Annuler</a>
        </div>
    </form>
</section>
