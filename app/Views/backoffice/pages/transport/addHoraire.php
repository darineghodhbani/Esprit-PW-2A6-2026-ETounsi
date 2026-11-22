<div class="page-header">
  <div>
    <h1 class="page-title">Ajouter un horaire</h1>
    <p class="page-subtitle">Tous les champs sont obligatoires. Les heures utilisent le format 24 h (HH:MM).</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-horaires" class="text-decoration-none text-body">Horaires</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Ajouter</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4 transport-form-card">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <?php if (!$lignes): ?>
    <div class="alert alert-warning" role="status">Ajoutez d’abord une ligne : chaque horaire appartient à une ligne.</div>
  <?php endif; ?>
  <form method="post" action="index.php?page=admin-horaire-add" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
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
        <label for="sens" class="form-label-custom">Sens</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'sens'); ?>" id="sens" name="sens" data-validate="required" required
                <?php if (isset($erreurs['sens'])): ?>aria-invalid="true" aria-describedby="erreur-sens"<?php endif; ?>>
          <option value="">-- Choisir le sens --</option>
          <?php foreach (Horaire::SENS as $s): ?>
            <option value="<?php echo e($s); ?>" <?php if ($old['sens'] === $s) echo 'selected'; ?>><?php echo e($s); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'sens'); ?>
      </div>

      <div class="col-12 col-md-4">
        <label for="heure_depart" class="form-label-custom">Heure de départ</label>
        <input type="time" class="form-control-custom<?php echo invalidClass($erreurs, 'heure_depart'); ?>" id="heure_depart" name="heure_depart"
               value="<?php echo e($old['heure_depart']); ?>" data-validate="required" required
               <?php if (isset($erreurs['heure_depart'])): ?>aria-invalid="true" aria-describedby="erreur-heure_depart"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'heure_depart'); ?>
      </div>

      <div class="col-12 col-md-4">
        <label for="heure_arrivee" class="form-label-custom">Heure d’arrivée</label>
        <input type="time" class="form-control-custom<?php echo invalidClass($erreurs, 'heure_arrivee'); ?>" id="heure_arrivee" name="heure_arrivee"
               value="<?php echo e($old['heure_arrivee']); ?>" data-validate="required" required
               <?php if (isset($erreurs['heure_arrivee'])): ?>aria-invalid="true" aria-describedby="erreur-heure_arrivee"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'heure_arrivee'); ?>
      </div>

      <div class="col-12 col-md-4">
        <label for="jours" class="form-label-custom">Jours de circulation</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'jours'); ?>" id="jours" name="jours" data-validate="required" required
                <?php if (isset($erreurs['jours'])): ?>aria-invalid="true" aria-describedby="erreur-jours"<?php endif; ?>>
          <option value="">-- Choisir les jours --</option>
          <?php foreach (Horaire::JOURS as $j): ?>
            <option value="<?php echo e($j); ?>" <?php if ($old['jours'] === $j) echo 'selected'; ?>><?php echo e($j); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'jours'); ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Ajouter</button>
        <a href="index.php?page=admin-horaires" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
