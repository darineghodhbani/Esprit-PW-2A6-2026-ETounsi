<div class="page-header">
  <div>
    <h1 class="page-title"><?php echo e($title); ?></h1>
    <p class="page-subtitle">Tous les champs sont obligatoires.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="admin.php?page=proprietes" class="text-decoration-none text-muted-green">Propriétés</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page"><?php echo $id ? 'Modifier' : 'Ajouter'; ?></li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($errors['global'])): ?>
    <div class="alert-custom alert-custom-danger"><?php echo e($errors['global']); ?></div>
  <?php endif; ?>

  <form method="post" action="admin.php?page=propriete-form<?php echo $id ? '&amp;id=' . $id : ''; ?>" novalidate data-validate-form>
    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="id_user" class="form-label-custom">Propriétaire (citoyen)</label>
        <select class="form-select-custom<?php echo invalidClass($errors, 'id_user'); ?>" id="id_user" name="id_user" data-validate="required">
          <option value="">-- Choisir un citoyen --</option>
          <?php foreach ($citoyens as $c): ?>
            <option value="<?php echo $c->getId(); ?>" <?php echo (int) $old['id_user'] === $c->getId() ? 'selected' : ''; ?>>
              <?php echo e($c->getNomComplet()); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($errors, 'id_user'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="type" class="form-label-custom">Type</label>
        <select class="form-select-custom<?php echo invalidClass($errors, 'type'); ?>" id="type" name="type" data-validate="required">
          <option value="">-- Choisir un type --</option>
          <?php foreach (ProprieteController::TYPES as $key => $label): ?>
            <option value="<?php echo e($key); ?>" <?php echo $old['type'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($errors, 'type'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="reference" class="form-label-custom">Référence (titre foncier ou immatriculation)</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'reference'); ?>" id="reference" name="reference"
               value="<?php echo e($old['reference']); ?>" placeholder="TF-123456" data-validate="required|min:3|max:50">
        <?php echo errorFor($errors, 'reference'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="valeur" class="form-label-custom">Valeur (DT)</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'valeur'); ?>" id="valeur" name="valeur"
               value="<?php echo e($old['valeur']); ?>" placeholder="250000" data-validate="required|number">
        <?php echo errorFor($errors, 'valeur'); ?>
      </div>
      <div class="col-12">
        <label for="adresse" class="form-label-custom">Adresse</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'adresse'); ?>" id="adresse" name="adresse"
               value="<?php echo e($old['adresse']); ?>" placeholder="12 rue de Marseille, Tunis" data-validate="required|min:5|max:150">
        <?php echo errorFor($errors, 'adresse'); ?>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn-custom btn-custom-primary" type="submit"><?php echo $id ? 'Enregistrer' : 'Ajouter'; ?></button>
        <a href="admin.php?page=proprietes" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
