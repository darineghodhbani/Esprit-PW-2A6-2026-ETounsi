<div class="page-header">
  <div>
    <h1 class="page-title"><?php echo e($title); ?></h1>
    <p class="page-subtitle">Tous les champs sont obligatoires.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="admin.php?page=impots" class="text-decoration-none text-muted-green">Impôts</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page"><?php echo $id ? 'Modifier' : 'Ajouter'; ?></li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($errors['global'])): ?>
    <div class="alert-custom alert-custom-danger"><?php echo e($errors['global']); ?></div>
  <?php endif; ?>

  <form method="post" action="admin.php?page=impot-form<?php echo $id ? '&amp;id=' . $id : ''; ?>" novalidate data-validate-form>
    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="id_propriete" class="form-label-custom">Propriété</label>
        <select class="form-select-custom<?php echo invalidClass($errors, 'id_propriete'); ?>" id="id_propriete" name="id_propriete" data-validate="required">
          <option value="">-- Choisir une propriété --</option>
          <?php foreach ($proprietes as $p): ?>
            <option value="<?php echo $p->getId(); ?>" <?php echo (int) $old['id_propriete'] === $p->getId() ? 'selected' : ''; ?>>
              <?php echo e($p->getReference() . ' (' . $p->getProprietaire() . ')'); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($errors, 'id_propriete'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="annee" class="form-label-custom">Année</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'annee'); ?>" id="annee" name="annee"
               value="<?php echo e($old['annee']); ?>" placeholder="2026" data-validate="required|number|min:4|max:4">
        <?php echo errorFor($errors, 'annee'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="montant" class="form-label-custom">Montant (DT)</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'montant'); ?>" id="montant" name="montant"
               value="<?php echo e($old['montant']); ?>" placeholder="340" data-validate="required|number">
        <?php echo errorFor($errors, 'montant'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="date_limite" class="form-label-custom">Date limite</label>
        <input type="date" class="form-control-custom<?php echo invalidClass($errors, 'date_limite'); ?>" id="date_limite" name="date_limite"
               value="<?php echo e($old['date_limite']); ?>" data-validate="required">
        <?php echo errorFor($errors, 'date_limite'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="statut" class="form-label-custom">Statut</label>
        <select class="form-select-custom<?php echo invalidClass($errors, 'statut'); ?>" id="statut" name="statut" data-validate="required">
          <?php foreach (ImpotController::STATUTS as $key => $label): ?>
            <option value="<?php echo e($key); ?>" <?php echo $old['statut'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($errors, 'statut'); ?>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn-custom btn-custom-primary" type="submit"><?php echo $id ? 'Enregistrer' : 'Ajouter'; ?></button>
        <a href="admin.php?page=impots" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
