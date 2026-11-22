<div class="page-header">
  <div>
    <h1 class="page-title">Colis</h1>
    <p class="page-subtitle">La Poste : déclarations et suivi des colis.</p>
  </div>
  <a href="index.php?page=addColis" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter un colis
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">En attente d’acceptation</span><i class="bi bi-clock" aria-hidden="true"></i></div>
      <div class="stat-value"><?php echo (int) $nbEnAttente; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">En acheminement</span><i class="bi bi-truck" aria-hidden="true"></i></div>
      <div class="stat-value"><?php echo (int) $nbEnAcheminement; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Livrés</span><i class="bi bi-check-circle" aria-hidden="true"></i></div>
      <div class="stat-value"><?php echo (int) $nbLivres; ?></div>
    </div>
  </div>
</div>

<div class="table-card-custom">
  <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des colis">
    <table class="table-custom">
      <caption class="visually-hidden">Colis déclarés</caption>
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Nº de suivi</th>
          <th scope="col">Bureau de départ</th>
          <th scope="col">Nom déclaré</th>
          <th scope="col">Date de déclaration</th>
          <th scope="col">Statut</th>
          <th scope="col" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$liste): ?>
          <tr><td colspan="7" class="text-center py-4">Aucun colis.</td></tr>
        <?php endif; ?>

        <?php foreach ($liste as $c): ?>
          <tr>
            <td class="table-order-id"><?php echo (int) $c['id_colis']; ?></td>
            <td><?php echo e($c['numero_suivi']); ?></td>
            <td><?php echo e($c['bureau']); ?></td>
            <td><?php echo e($c['nom_citoyen']); ?><br><small>Compte nº <?php echo (int) $c['id_citoyen']; ?></small></td>
            <td><?php echo e(date('d/m/Y', strtotime($c['date_declaration']))); ?></td>
            <td>
              <?php
                $badge = 'failed';
                if ($c['statut'] === 'Livré') $badge = 'success';
                if (in_array($c['statut'], ['En attente', 'Accepté', 'En transit'], true)) $badge = 'pending';
              ?>
              <span class="badge-table <?php echo $badge; ?>"><?php echo e($c['statut']); ?></span>
            </td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="index.php?page=updateColis&amp;id=<?php echo (int) $c['id_colis']; ?>" class="table-btn-action" title="Modifier" aria-label="Modifier le statut du colis <?php echo e($c['numero_suivi']); ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                <form method="post" action="index.php?page=deleteColis" class="d-inline"
                      data-confirm="Supprimer ce colis ?">
                  <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo (int) $c['id_colis']; ?>">
                  <button type="submit" class="table-btn-action delete border-0" title="Supprimer" aria-label="Supprimer le colis <?php echo e($c['numero_suivi']); ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
