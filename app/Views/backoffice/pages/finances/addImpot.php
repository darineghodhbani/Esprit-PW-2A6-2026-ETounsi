<div class="page-header">
  <div>
    <h1 class="page-title">Ajouter un impôt</h1>
    <p class="page-subtitle">Tous les champs sont obligatoires.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-impots" class="text-decoration-none text-muted-green">Impôts</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Ajouter</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <form method="post" action="index.php?page=admin-impot-add" novalidate data-validate-form>
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="id_propriete" class="form-label-custom">Propriété</label>
        <select class="form-select-custom" id="id_propriete" name="id_propriete" data-validate="required">
          <option value="">-- Choisir une propriété --</option>
          <?php foreach ($proprietes as $p): ?>
            <option value="<?php echo $p['id']; ?>" <?php if ($old['id_propriete'] == $p['id']) echo 'selected'; ?>>
              <?php echo e($p['reference'] . ' — ' . $p['proprietaire']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['id_propriete'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['id_propriete']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="annee" class="form-label-custom">Année</label>
        <input type="text" class="form-control-custom" id="annee" name="annee"
               value="<?php echo e($old['annee']); ?>" placeholder="2026" data-validate="required|number|min:4|max:4">
        <?php if (isset($erreurs['annee'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['annee']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="montant" class="form-label-custom">Montant (DT)</label>
        <input type="text" class="form-control-custom" id="montant" name="montant"
               value="<?php echo e($old['montant']); ?>" placeholder="340" data-validate="required|number">
        <?php if (isset($erreurs['montant'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['montant']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="date_limite" class="form-label-custom">Date limite</label>
        <input type="date" class="form-control-custom" id="date_limite" name="date_limite"
               value="<?php echo e($old['date_limite']); ?>" data-validate="required|date">
        <?php if (isset($erreurs['date_limite'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['date_limite']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="statut" class="form-label-custom">Statut</label>
        <select class="form-select-custom" id="statut" name="statut" data-validate="required">
          <?php foreach (ImpotController::STATUTS as $key => $label): ?>
            <option value="<?php echo $key; ?>" <?php if ($old['statut'] == $key) echo 'selected'; ?>><?php echo $label; ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['statut'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['statut']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn-custom btn-custom-primary" type="submit">Ajouter</button>
        <a href="index.php?page=admin-impots" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
