<div class="page-header">
  <div>
    <h1 class="page-title">Ajouter un bureau</h1>
    <p class="page-subtitle">Renseignez les informations du formulaire.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-bureaux" class="text-decoration-none text-body">Bureaux de poste</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Ajouter</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <form method="post" action="index.php?page=admin-bureau-add" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="nom" class="form-label-custom">Nom du bureau</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'nom'); ?>" id="nom" name="nom"
               value="<?php echo e($old['nom']); ?>" data-validate="required|max:100" required maxlength="100"
               <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'nom'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="adresse" class="form-label-custom">Adresse</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'adresse'); ?>" id="adresse" name="adresse"
               value="<?php echo e($old['adresse']); ?>" data-validate="required|max:200" required maxlength="200"
               <?php if (isset($erreurs['adresse'])): ?>aria-invalid="true" aria-describedby="erreur-adresse"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'adresse'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="ville" class="form-label-custom">Ville</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'ville'); ?>" id="ville" name="ville"
               value="<?php echo e($old['ville']); ?>" data-validate="required|max:80" required maxlength="80"
               <?php if (isset($erreurs['ville'])): ?>aria-invalid="true" aria-describedby="erreur-ville"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'ville'); ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Ajouter</button>
        <a href="index.php?page=admin-bureaux" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
