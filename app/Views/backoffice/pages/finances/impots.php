<div class="page-header">
  <div>
    <h1 class="page-title">Impôts</h1>
    <p class="page-subtitle">Pour valider un paiement : Modifier → statut « Payé ».</p>
  </div>
  <a href="index.php?page=admin-impot-add" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg"></i> Ajouter un impôt
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-md-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Non payés</span>
        <span class="stat-icone stat-rouge"><i class="bi bi-x-circle-fill"></i></span>
      </div>
      <div class="stat-value"><?php echo $nbNonPayes; ?></div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">En attente de validation</span>
        <span class="stat-icone stat-jaune"><i class="bi bi-hourglass-split"></i></span>
      </div>
      <div class="stat-value"><?php echo $nbEnAttente; ?></div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Payés</span>
        <span class="stat-icone stat-gris"><i class="bi bi-check-circle-fill"></i></span>
      </div>
      <div class="stat-value"><?php echo $nbPayes; ?></div>
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
        <?php if (count($liste) == 0): ?>
          <tr><td colspan="7" class="text-center py-4">Aucun impôt.</td></tr>
        <?php endif; ?>

        <?php foreach ($liste as $i): ?>
          <tr>
            <td class="table-order-id"><?php echo $i['id']; ?></td>
            <td><?php echo e($i['reference']); ?></td>
            <td><?php echo $i['annee']; ?></td>
            <td><?php echo $i['montant']; ?></td>
            <td><?php echo $i['date_limite']; ?></td>
            <td>
              <?php if ($i['statut'] == 'paye'): ?>
                <span class="badge bg-success">Payé</span>
              <?php elseif ($i['statut'] == 'en_attente'): ?>
                <span class="badge bg-warning text-dark">En attente de validation</span>
              <?php else: ?>
                <span class="badge bg-danger">Non payé</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="index.php?page=admin-impot-update&id=<?php echo $i['id']; ?>" class="table-btn-action" title="Modifier"><i class="bi bi-pencil"></i></a>
                <form method="post" action="index.php?page=admin-impot-delete" class="d-inline"
                      data-confirm="Supprimer cet impôt ?">
                  <input type="hidden" name="id" value="<?php echo $i['id']; ?>">
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
