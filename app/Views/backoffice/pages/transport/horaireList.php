<?php $ajout = 'index.php?page=admin-horaire-add' . ($filtre > 0 ? '&ligne=' . $filtre : ''); ?>
<div class="page-header">
  <div>
    <h1 class="page-title">Horaires</h1>
    <p class="page-subtitle">Transport : départs, arrivées et jours de circulation affichés aux citoyens.</p>
  </div>
  <a href="<?php echo e($ajout); ?>" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter un horaire
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Horaires enregistrés</span><span class="stat-icone stat-rouge"><i class="bi bi-clock" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo (int) $stats['total']; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Circulent aujourd’hui</span><span class="stat-icone stat-jaune"><i class="bi bi-calendar-check" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo (int) $nbAujourdhui; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Aller / Retour</span><span class="stat-icone stat-gris"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo (int) $stats['aller']; ?> <small class="stat-unit">/ <?php echo (int) $stats['retour']; ?></small></div>
    </div>
  </div>
</div>

<section class="table-card-custom" aria-label="Horaires">
  <form method="get" action="index.php" class="table-header-control transport-filter" novalidate>
    <input type="hidden" name="page" value="admin-horaires">
    <div class="transport-filter-field">
      <label for="ligne" class="form-label-custom">Ligne</label>
      <select class="form-select-custom" id="ligne" name="ligne">
        <option value="0">Toutes les lignes</option>
        <?php foreach ($lignes as $l): ?>
          <option value="<?php echo (int) $l['id_ligne']; ?>" <?php if ((int) $l['id_ligne'] === $filtre) echo 'selected'; ?>><?php echo e($l['numero'] . ' · ' . $l['nom']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="transport-filter-actions">
      <button class="btn-custom btn-custom-light" type="submit"><i class="bi bi-funnel" aria-hidden="true"></i> Filtrer</button>
      <?php if ($filtre > 0): ?>
        <a href="index.php?page=admin-horaires" class="btn-custom btn-custom-light text-decoration-none">Effacer</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des horaires">
    <table class="table-custom">
      <caption class="visually-hidden">Horaires des lignes</caption>
      <thead>
        <tr>
          <th scope="col">Ligne</th>
          <th scope="col">Sens</th>
          <th scope="col">Départ</th>
          <th scope="col">Arrivée</th>
          <th scope="col">Jours</th>
          <th scope="col" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$liste): ?>
          <tr><td colspan="6" class="text-center py-4">Aucun horaire. Ajoutez le premier horaire.</td></tr>
        <?php endif; ?>

        <?php foreach ($liste as $h): ?>
          <?php $libelle = $h['numero'] . ' à ' . HoraireController::heure($h['heure_depart']); ?>
          <tr>
            <td><span class="numero-ligne"><?php echo e($h['numero']); ?></span> <?php echo e($h['ligne_nom']); ?></td>
            <td><span class="badge-table <?php echo $h['sens'] === 'Aller' ? 'success' : 'pending'; ?>"><?php echo e($h['sens']); ?></span></td>
            <td><strong><?php echo e(HoraireController::heure($h['heure_depart'])); ?></strong></td>
            <td><?php echo e(HoraireController::heure($h['heure_arrivee'])); ?></td>
            <td><?php echo e($h['jours']); ?></td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="index.php?page=admin-horaire-update&amp;id=<?php echo (int) $h['id_horaire']; ?>" class="table-btn-action" title="Modifier" aria-label="Modifier l’horaire <?php echo e($libelle); ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                <form method="post" action="index.php?page=admin-horaire-delete" class="d-inline" data-confirm="Supprimer l’horaire <?php echo e($libelle); ?> ?">
                  <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo (int) $h['id_horaire']; ?>">
                  <input type="hidden" name="ligne" value="<?php echo (int) $filtre; ?>">
                  <button type="submit" class="table-btn-action delete border-0" title="Supprimer" aria-label="Supprimer l’horaire <?php echo e($libelle); ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
