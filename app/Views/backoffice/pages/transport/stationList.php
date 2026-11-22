<?php $ajout = 'index.php?page=admin-station-add' . ($filtre > 0 ? '&ligne=' . $filtre : ''); ?>
<div class="page-header">
  <div>
    <h1 class="page-title">Stations</h1>
    <p class="page-subtitle">Transport : stations de chaque ligne et ordre de passage.</p>
  </div>
  <a href="<?php echo e($ajout); ?>" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter une station
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-sm-6">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Stations enregistrées</span><span class="stat-icone stat-rouge"><i class="bi bi-geo-alt" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo (int) $stats['total']; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-6">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Lignes desservies</span><span class="stat-icone stat-jaune"><i class="bi bi-bus-front" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo (int) $stats['lignes']; ?> <small class="stat-unit">/ <?php echo count($lignes); ?></small></div>
    </div>
  </div>
</div>

<section class="table-card-custom" aria-label="Stations">
  <form method="get" action="index.php" class="table-header-control transport-filter" novalidate>
    <input type="hidden" name="page" value="admin-stations">
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
        <a href="index.php?page=admin-stations" class="btn-custom btn-custom-light text-decoration-none">Effacer</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des stations">
    <table class="table-custom">
      <caption class="visually-hidden">Stations des lignes</caption>
      <thead>
        <tr>
          <th scope="col">Ligne</th>
          <th scope="col">Ordre</th>
          <th scope="col">Station</th>
          <th scope="col">Adresse</th>
          <th scope="col" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$liste): ?>
          <tr><td colspan="5" class="text-center py-4">Aucune station. Ajoutez la première station.</td></tr>
        <?php endif; ?>

        <?php foreach ($liste as $s): ?>
          <tr>
            <td><span class="numero-ligne"><?php echo e($s['numero']); ?></span> <?php echo e($s['ligne_nom']); ?></td>
            <td><span class="ordre-badge"><?php echo (int) $s['ordre']; ?></span></td>
            <td class="table-product-name"><?php echo e($s['nom']); ?></td>
            <td><?php echo $s['adresse'] !== null && $s['adresse'] !== '' ? e($s['adresse']) : '—'; ?></td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="index.php?page=admin-station-update&amp;id=<?php echo (int) $s['id_station']; ?>" class="table-btn-action" title="Modifier" aria-label="Modifier la station <?php echo e($s['nom']); ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                <form method="post" action="index.php?page=admin-station-delete" class="d-inline" data-confirm="Supprimer la station « <?php echo e($s['nom']); ?> » ?">
                  <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo (int) $s['id_station']; ?>">
                  <input type="hidden" name="ligne" value="<?php echo (int) $filtre; ?>">
                  <button type="submit" class="table-btn-action delete border-0" title="Supprimer" aria-label="Supprimer la station <?php echo e($s['nom']); ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
