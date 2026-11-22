<div class="page-header">
  <div>
    <h1 class="page-title">Propriétés</h1>
    <p class="page-subtitle">Recette des Finances : <?php echo $total; ?> propriété(s) enregistrée(s).</p>
  </div>
  <a href="index.php?page=admin-propriete-add" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg"></i> Ajouter une propriété
  </a>
</div>

<div class="table-card-custom">
  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>ID</th>
          <th>Référence</th>
          <th>Type</th>
          <th>Propriétaire</th>
          <th>Adresse</th>
          <th>Valeur (DT)</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($liste) == 0): ?>
          <tr><td colspan="7" class="text-center py-4">Aucune propriété.</td></tr>
        <?php endif; ?>

        <?php foreach ($liste as $p): ?>
          <tr>
            <td class="table-order-id"><?php echo $p['id']; ?></td>
            <td><?php echo e($p['reference']); ?></td>
            <td><?php echo e(ProprieteController::TYPES[$p['type']]); ?></td>
            <td><?php echo e($p['proprietaire']); ?></td>
            <td><?php echo e($p['adresse']); ?></td>
            <td><?php echo $p['valeur']; ?></td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="index.php?page=admin-propriete-update&id=<?php echo $p['id']; ?>" class="table-btn-action" title="Modifier"><i class="bi bi-pencil"></i></a>
                <form method="post" action="index.php?page=admin-propriete-delete" class="d-inline"
                      data-confirm="Supprimer cette propriété et tous ses impôts ?">
<input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                  <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                  <button type="submit" class="table-btn-action delete border-0" title="Supprimer"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
