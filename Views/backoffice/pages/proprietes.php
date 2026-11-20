<div class="page-header">
  <div>
    <h1 class="page-title">Propriétés</h1>
    <p class="page-subtitle">Recette des Finances : <?php echo (int) $total; ?> propriété(s) enregistrée(s).</p>
  </div>
  <a href="admin.php?page=propriete-form" class="btn-custom btn-custom-primary text-decoration-none">
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
        <?php if (!$list): ?>
          <tr><td colspan="7" class="text-center py-4">Aucune propriété.</td></tr>
        <?php endif; ?>

        <?php foreach ($list as $p): ?>
          <tr>
            <td class="table-order-id"><?php echo $p->getId(); ?></td>
            <td><?php echo e($p->getReference()); ?></td>
            <td><?php echo e(ProprieteController::TYPES[$p->getType()] ?? $p->getType()); ?></td>
            <td><?php echo e($p->getProprietaire()); ?></td>
            <td><?php echo e($p->getAdresse()); ?></td>
            <td><?php echo $p->getValeur(); ?></td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="admin.php?page=propriete-form&amp;id=<?php echo $p->getId(); ?>" class="table-btn-action" title="Modifier"><i class="bi bi-pencil"></i></a>
                <form method="post" action="admin.php?page=propriete-delete" class="d-inline"
                      data-confirm="Supprimer cette propriété et tous ses impôts ?">
                  <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo $p->getId(); ?>">
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
