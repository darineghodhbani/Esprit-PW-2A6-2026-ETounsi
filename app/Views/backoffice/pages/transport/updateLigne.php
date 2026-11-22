<div class="page-header">
  <div>
    <h1 class="page-title">Modifier une ligne</h1>
    <p class="page-subtitle">Tous les champs sont obligatoires.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-lignes" class="text-decoration-none text-body">Lignes de transport</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4 transport-form-card">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <p class="transport-form-context">
    <i class="bi bi-info-circle" aria-hidden="true"></i>
    Cette ligne compte <a href="index.php?page=admin-stations&amp;ligne=<?php echo (int) $id; ?>"><?php echo (int) $nbStations; ?> station(s)</a>
    et <a href="index.php?page=admin-horaires&amp;ligne=<?php echo (int) $id; ?>"><?php echo (int) $nbHoraires; ?> horaire(s)</a>.
  </p>
  <form method="post" action="index.php?page=admin-ligne-update&amp;id=<?php echo (int) $id; ?>" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <label for="numero" class="form-label-custom">Numéro de la ligne</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'numero'); ?>" id="numero" name="numero"
               value="<?php echo e($old['numero']); ?>" data-validate="required|max:20" required maxlength="20"
               <?php if (isset($erreurs['numero'])): ?>aria-invalid="true" aria-describedby="erreur-numero"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'numero'); ?>
      </div>

      <div class="col-12 col-md-8">
        <label for="nom" class="form-label-custom">Nom de la ligne</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'nom'); ?>" id="nom" name="nom"
               value="<?php echo e($old['nom']); ?>" data-validate="required|max:100" required maxlength="100"
               <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'nom'); ?>
      </div>

      <div class="col-12 col-md-4">
        <label for="tarif" class="form-label-custom">Tarif (DT)</label>
        <input type="text" inputmode="decimal" class="form-control-custom<?php echo invalidClass($erreurs, 'tarif'); ?>" id="tarif" name="tarif"
               value="<?php echo e($old['tarif']); ?>" data-validate="required|max:6" required maxlength="6"
               aria-describedby="aide-tarif<?php echo isset($erreurs['tarif']) ? ' erreur-tarif' : ''; ?>"<?php if (isset($erreurs['tarif'])) echo ' aria-invalid="true"'; ?>>
        <div class="form-text" id="aide-tarif">Entre 0 et 999.99, deux décimales au plus.</div>
        <?php echo errorFor($erreurs, 'tarif'); ?>
      </div>

      <div class="col-12 col-md-4">
        <label for="actif" class="form-label-custom">État</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'actif'); ?>" id="actif" name="actif" data-validate="required" required
                <?php if (isset($erreurs['actif'])): ?>aria-invalid="true" aria-describedby="erreur-actif"<?php endif; ?>>
          <option value="1" <?php if ($old['actif'] === '1') echo 'selected'; ?>>Active (visible par les citoyens)</option>
          <option value="0" <?php if ($old['actif'] === '0') echo 'selected'; ?>>Inactive (masquée)</option>
        </select>
        <?php echo errorFor($erreurs, 'actif'); ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
        <a href="index.php?page=admin-lignes" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
