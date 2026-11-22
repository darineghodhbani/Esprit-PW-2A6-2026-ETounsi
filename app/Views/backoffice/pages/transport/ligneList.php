<div class="page-header">
  <div>
    <h1 class="page-title">Lignes de transport</h1>
    <p class="page-subtitle">Transport : numéros, noms et tarifs des lignes affichées aux citoyens.</p>
  </div>
  <a href="index.php?page=admin-ligne-add" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter une ligne
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Lignes enregistrées</span><span class="stat-icone stat-rouge"><i class="bi bi-bus-front" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo (int) $stats['total']; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Lignes actives</span><span class="stat-icone stat-jaune"><i class="bi bi-check-circle" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo (int) $stats['actives']; ?> <small class="stat-unit">/ <?php echo (int) $stats['total']; ?></small></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Tarifs</span><span class="stat-icone stat-gris"><i class="bi bi-ticket-perforated" aria-hidden="true"></i></span></div>
      <div class="stat-value"><?php echo e(number_format((float) $stats['tarif_min'], 3)); ?> <small class="stat-unit">à <?php echo e(number_format((float) $stats['tarif_max'], 3)); ?> DT</small></div>
    </div>
  </div>
</div>

<section class="table-card-custom" aria-label="Lignes de transport">
  <form method="get" action="index.php" class="table-header-control transport-filter" novalidate>
    <input type="hidden" name="page" value="admin-lignes">
    <div class="transport-filter-field">
      <label for="q" class="form-label-custom">Rechercher une ligne</label>
      <input type="search" class="form-control-custom" id="q" name="q" maxlength="100" value="<?php echo e($recherche); ?>" placeholder="Numéro ou nom">
    </div>
    <div class="transport-filter-actions">
      <button class="btn-custom btn-custom-light" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Rechercher</button>
      <?php if ($recherche !== ''): ?>
        <a href="index.php?page=admin-lignes" class="btn-custom btn-custom-light text-decoration-none">Effacer</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des lignes">
    <table class="table-custom">
      <caption class="visually-hidden">Lignes de transport</caption>
      <thead>
        <tr>
          <th scope="col">Numéro</th>
          <th scope="col">Ligne</th>
          <th scope="col">Tarif (DT)</th>
          <th scope="col">État</th>
          <th scope="col">Stations</th>
          <th scope="col">Horaires</th>
          <th scope="col" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$liste): ?>
          <tr><td colspan="7" class="text-center py-4"><?php echo $recherche === '' ? 'Aucune ligne. Ajoutez la première ligne.' : 'Aucune ligne trouvée pour cette recherche.'; ?></td></tr>
        <?php endif; ?>

        <?php foreach ($liste as $l): ?>
          <?php
          $confirmation = 'Supprimer la ligne ' . $l['numero'] . ' ? Ses ' . (int) $l['nb_stations'] . ' station(s) et ses '
              . (int) $l['nb_horaires'] . ' horaire(s) seront aussi supprimés.';
          ?>
          <tr>
            <td><span class="numero-ligne"><?php echo e($l['numero']); ?></span></td>
            <td class="table-product-name"><?php echo e($l['nom']); ?></td>
            <td class="table-amount"><?php echo e(number_format((float) $l['tarif'], 3)); ?></td>
            <td>
              <?php if ((int) $l['actif'] === 1): ?>
                <span class="badge-table success">Active</span>
              <?php else: ?>
                <span class="badge-table failed">Inactive</span>
              <?php endif; ?>
            </td>
            <td><a class="count-link" href="index.php?page=admin-stations&amp;ligne=<?php echo (int) $l['id_ligne']; ?>" aria-label="Voir les stations de la ligne <?php echo e($l['numero']); ?>"><?php echo (int) $l['nb_stations']; ?> <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></td>
            <td><a class="count-link" href="index.php?page=admin-horaires&amp;ligne=<?php echo (int) $l['id_ligne']; ?>" aria-label="Voir les horaires de la ligne <?php echo e($l['numero']); ?>"><?php echo (int) $l['nb_horaires']; ?> <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="index.php?page=admin-ligne-update&amp;id=<?php echo (int) $l['id_ligne']; ?>" class="table-btn-action" title="Modifier" aria-label="Modifier la ligne <?php echo e($l['numero']); ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                <form method="post" action="index.php?page=admin-ligne-delete" class="d-inline" data-confirm="<?php echo e($confirmation); ?>">
                  <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo (int) $l['id_ligne']; ?>">
                  <button type="submit" class="table-btn-action delete border-0" title="Supprimer" aria-label="Supprimer la ligne <?php echo e($l['numero']); ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
