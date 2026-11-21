<div class="page-header">
  <div>
    <h1 class="page-title">Modifier une propriété</h1>
    <p class="page-subtitle">Tous les champs sont obligatoires.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-proprietes" class="text-decoration-none text-muted-green">Propriétés</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <form method="post" action="index.php?page=admin-propriete-update&id=<?php echo $id; ?>" novalidate data-validate-form>
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="id_citoyen" class="form-label-custom">Propriétaire (citoyen)</label>
        <select class="form-select-custom" id="id_citoyen" name="id_citoyen" data-validate="required">
          <option value="">-- Choisir un citoyen --</option>
          <?php foreach ($citoyens as $c): ?>
            <option value="<?php echo $c['id_citoyen']; ?>" <?php if ($old['id_citoyen'] == $c['id_citoyen']) echo 'selected'; ?>>
              <?php echo e($c['prenom'] . ' ' . $c['nom']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['id_citoyen'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['id_citoyen']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="type" class="form-label-custom">Type</label>
        <select class="form-select-custom" id="type" name="type" data-validate="required">
          <option value="">-- Choisir un type --</option>
          <?php foreach (ProprieteController::TYPES as $key => $label): ?>
            <option value="<?php echo $key; ?>" <?php if ($old['type'] == $key) echo 'selected'; ?>><?php echo $label; ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['type'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['type']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="reference" class="form-label-custom">Référence (titre foncier ou immatriculation)</label>
        <input type="text" class="form-control-custom" id="reference" name="reference"
               value="<?php echo e($old['reference']); ?>" placeholder="TF-123456" data-validate="required|min:3|max:50">
        <?php if (isset($erreurs['reference'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['reference']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="valeur" class="form-label-custom">Valeur (DT)</label>
        <input type="text" class="form-control-custom" id="valeur" name="valeur"
               value="<?php echo e($old['valeur']); ?>" placeholder="250000" data-validate="required|number">
        <?php if (isset($erreurs['valeur'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['valeur']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12">
        <label for="adresse" class="form-label-custom">Adresse</label>
        <input type="text" class="form-control-custom" id="adresse" name="adresse"
               value="<?php echo e($old['adresse']); ?>" placeholder="12 rue de Marseille, Tunis" data-validate="required|min:5|max:150">
        <?php if (isset($erreurs['adresse'])): ?>
          <div class="invalid-feedback d-block server-error"><?php echo $erreurs['adresse']; ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
        <a href="index.php?page=admin-proprietes" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
