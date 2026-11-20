<div class="page-header">
  <div>
    <h1 class="page-title">Impôts</h1>
    <p class="page-subtitle">Recette des Finances : impôts annuels des propriétés.</p>
  </div>
  <a href="admin.php?page=impot-form" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg"></i> Ajouter un impôt
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Impôts payés</span><i class="bi bi-check-circle text-muted-green"></i></div>
      <div class="stat-value"><?php echo (int) $nbPayes; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">En attente de validation</span><i class="bi bi-clock text-muted-green"></i></div>
      <div class="stat-value"><?php echo (int) $nbEnAttente; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Impôts non payés</span><i class="bi bi-hourglass-split text-muted-green"></i></div>
      <div class="stat-value"><?php echo (int) $nbNonPayes; ?></div>
    </div>
  </div>
</div>

<div class="table-card-custom">
  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>ID</th>
          <th>Propriété</th>
          <th>Année</th>
          <th>Montant (DT)</th>
          <th>Date limite</th>
          <th>Statut</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$list): ?>
          <tr><td colspan="7" class="text-center py-4">Aucun impôt.</td></tr>
        <?php endif; ?>

        <?php foreach ($list as $i): ?>
          <tr>
            <td class="table-order-id"><?php echo $i->getId(); ?></td>
            <td><?php echo e($i->getReference()); ?></td>
            <td><?php echo $i->getAnnee(); ?></td>
            <td><?php echo $i->getMontant(); ?></td>
            <td><?php echo e($i->getDateLimite()); ?></td>
            <td>
              <?php
                $badge = 'failed';
                if ($i->getStatut() === 'paye') $badge = 'success';
                if ($i->getStatut() === 'en_attente') $badge = 'pending';
              ?>
              <span class="badge-table <?php echo $badge; ?>">
                <?php echo e(ImpotController::STATUTS[$i->getStatut()] ?? $i->getStatut()); ?>
              </span>
            </td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="admin.php?page=impot-form&amp;id=<?php echo $i->getId(); ?>" class="table-btn-action" title="Modifier"><i class="bi bi-pencil"></i></a>
                <form method="post" action="admin.php?page=impot-delete" class="d-inline"
                      data-confirm="Supprimer cet impôt ?">
                  <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo $i->getId(); ?>">
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
