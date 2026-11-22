<div class="page-header">
  <div>
    <h1 class="page-title">Ajouter un colis</h1>
    <p class="page-subtitle">Renseignez les informations du formulaire.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-colis" class="text-decoration-none text-body">Colis</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Ajouter</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <?php if (count($bureaux) == 0): ?>
    <p class="alert alert-warning">Ajoutez un bureau de poste avant d’enregistrer un colis.</p>
    <a class="btn-custom btn-custom-primary" href="index.php?page=admin-bureau-add">Ajouter un bureau</a>
  <?php elseif (count($citoyens) == 0): ?>
    <p class="alert alert-warning">Aucun compte citoyen disponible pour enregistrer un colis.</p>
  <?php else: ?>
    <p class="text-main">Le code de suivi sera généré automatiquement et le statut initial sera « En attente ».</p>
  <form method="post" action="index.php?page=admin-colis-add" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="id_bureau_depart" class="form-label-custom">Bureau de départ</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'id_bureau_depart'); ?>" id="id_bureau_depart" name="id_bureau_depart" data-validate="required" required
                <?php if (isset($erreurs['id_bureau_depart'])): ?>aria-invalid="true" aria-describedby="erreur-id_bureau_depart"<?php endif; ?>>
          <option value="">-- Choisir un bureau --</option>
          <?php foreach ($bureaux as $b): ?>
            <option value="<?php echo e($b['id_bureau']); ?>" <?php if ((string) $old['id_bureau_depart'] === (string) $b['id_bureau']) echo 'selected'; ?>>
              <?php echo e($b['nom'] . ' — ' . $b['ville']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'id_bureau_depart'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="id_citoyen" class="form-label-custom">Compte citoyen</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'id_citoyen'); ?>" id="id_citoyen" name="id_citoyen" data-validate="required" required
                <?php if (isset($erreurs['id_citoyen'])): ?>aria-invalid="true" aria-describedby="erreur-id_citoyen"<?php endif; ?>>
          <option value="">-- Choisir un citoyen --</option>
          <?php foreach ($citoyens as $c): ?>
            <option value="<?php echo e($c['id_citoyen']); ?>" <?php if ((string) $old['id_citoyen'] === (string) $c['id_citoyen']) echo 'selected'; ?>>
              <?php echo e($c['prenom'] . ' ' . $c['nom']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'id_citoyen'); ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Ajouter</button>
        <a href="index.php?page=admin-colis" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
  <?php endif; ?>
</div>
