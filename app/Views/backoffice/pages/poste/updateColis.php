<div class="page-header">
  <div>
    <h1 class="page-title">Modifier le statut du colis</h1>
    <p class="page-subtitle">Choisissez le prochain état du colis.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-colis" class="text-decoration-none text-body">Colis</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <dl class="poste-details">
    <div><dt>Nº de suivi</dt><dd><?php echo e($old['numero_suivi']); ?></dd></div>
    <div><dt>Bureau de départ</dt><dd><?php echo e($old['bureau']); ?></dd></div>
    <div><dt>Nom déclaré</dt><dd><?php echo e($old['nom_citoyen']); ?></dd></div>
    <div><dt>Date de déclaration</dt><dd><?php echo e(date('d/m/Y', strtotime($old['date_declaration']))); ?></dd></div>
    <div><dt>État actuel</dt><dd><?php echo e($old['statut']); ?></dd></div>
  </dl>
  <?php if (count($statuts) == 0): ?>
    <?php if (isset($erreurs['statut'])): ?><p class="alert alert-danger" role="alert"><?php echo e($erreurs['statut']); ?></p><?php endif; ?>
    <p class="alert alert-info">Ce colis est <?php echo e($old['statut']); ?>. Son état ne peut plus être modifié.</p>
  <?php else: ?>
    <p class="text-main">Le traitement suit les étapes : En attente → Accepté → En transit → Livré.</p>
  <form method="post" action="index.php?page=admin-colis-update&amp;id=<?php echo e($id); ?>" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <input type="hidden" name="id" value="<?php echo e($id); ?>">
    <input type="hidden" name="statut_actuel" value="<?php echo e($old['statut']); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="statut" class="form-label-custom">Prochain état du colis</label>
        <select class="form-select-custom<?php if (isset($erreurs['statut'])) echo ' is-invalid'; ?>" id="statut" name="statut" data-validate="required" required
                <?php if (isset($erreurs['statut'])): ?>aria-invalid="true" aria-describedby="erreur-statut"<?php endif; ?>>
          <option value="">-- Choisir la prochaine étape --</option>
          <?php foreach ($statuts as $statut): ?>
            <option value="<?php echo e($statut); ?>" <?php if ($statutChoisi === $statut) echo 'selected'; ?>><?php echo e($statut); ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['statut'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-statut" role="alert"><?php echo e($erreurs['statut']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
        <a href="index.php?page=admin-colis" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
  <?php endif; ?>
</div>
