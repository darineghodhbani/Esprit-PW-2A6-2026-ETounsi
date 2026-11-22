<div class="page-header">
  <div>
    <h1 class="page-title">Modifier une station</h1>
    <p class="page-subtitle">L’adresse est facultative ; les autres champs sont obligatoires.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-stations" class="text-decoration-none text-body">Stations</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4 transport-form-card">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <form method="post" action="index.php?page=admin-station-update&amp;id=<?php echo (int) $id; ?>" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
    <div class="row g-3">
      <div class="col-12 col-md-8">
        <label for="id_ligne" class="form-label-custom">Ligne</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'id_ligne'); ?>" id="id_ligne" name="id_ligne" data-validate="required" required
                <?php if (isset($erreurs['id_ligne'])): ?>aria-invalid="true" aria-describedby="erreur-id_ligne"<?php endif; ?>>
          <option value="">-- Choisir une ligne --</option>
          <?php foreach ($lignes as $l): ?>
            <option value="<?php echo (int) $l['id_ligne']; ?>" <?php if ($old['id_ligne'] === (string) $l['id_ligne']) echo 'selected'; ?>><?php echo e($l['numero'] . ' · ' . $l['nom']); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'id_ligne'); ?>
      </div>

      <div class="col-12 col-md-4">
        <label for="ordre" class="form-label-custom">Ordre de passage</label>
        <input type="text" inputmode="numeric" class="form-control-custom<?php echo invalidClass($erreurs, 'ordre'); ?>" id="ordre" name="ordre"
               value="<?php echo e($old['ordre']); ?>" data-validate="required|number|max:3" required maxlength="3"
               <?php if (isset($erreurs['ordre'])): ?>aria-invalid="true" aria-describedby="erreur-ordre"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'ordre'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="nom" class="form-label-custom">Nom de la station</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'nom'); ?>" id="nom" name="nom"
               value="<?php echo e($old['nom']); ?>" data-validate="required|max:100" required maxlength="100"
               <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'nom'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="adresse" class="form-label-custom">Adresse (facultatif)</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'adresse'); ?>" id="adresse" name="adresse"
               value="<?php echo e($old['adresse']); ?>" data-validate="max:255" maxlength="255"
               <?php if (isset($erreurs['adresse'])): ?>aria-invalid="true" aria-describedby="erreur-adresse"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'adresse'); ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
        <a href="index.php?page=admin-stations&amp;ligne=<?php echo (int) $old['id_ligne']; ?>" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
