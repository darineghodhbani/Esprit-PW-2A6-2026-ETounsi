<?php
require_once __DIR__ . '/../../../../Controllers/CitoyenController.php';
require_once __DIR__ . '/../../../../Controllers/BureauPostalController.php';
require_once __DIR__ . '/../../../../Controllers/ColisController.php';
CitoyenController::requireAdmin();

$bureauC = new BureauPostalController();
$colisC = new ColisController();
$liste = $bureauC->listerBureaux();
$nbBureaux = $bureauC->compterBureaux();
$nbVilles = $bureauC->compterVilles();
$nbColis = $colisC->compterColis();

$title = 'E-Tounsi Admin - Bureaux de poste';
$currentPage = 'admin-bureaux';
$section = 'bureaux';
$csrfToken = csrfToken();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Bureaux de poste</h1>
    <p class="page-subtitle">La Poste : coordonnées des bureaux disponibles pour les citoyens.</p>
  </div>
  <a href="index.php?page=addBureau" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter un bureau
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Bureaux enregistrés</span><i class="bi bi-building" aria-hidden="true"></i></div>
      <div class="stat-value"><?php echo (int) $nbBureaux; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Villes desservies</span><i class="bi bi-geo-alt" aria-hidden="true"></i></div>
      <div class="stat-value"><?php echo (int) $nbVilles; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-4">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Colis déclarés</span><i class="bi bi-box-seam" aria-hidden="true"></i></div>
      <div class="stat-value"><?php echo (int) $nbColis; ?></div>
    </div>
  </div>
</div>

<div class="table-card-custom">
  <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des bureaux">
    <table class="table-custom">
      <caption class="visually-hidden">Bureaux de poste</caption>
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Bureau</th>
          <th scope="col">Adresse</th>
          <th scope="col">Ville</th>
          <th scope="col" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$liste): ?>
          <tr><td colspan="5" class="text-center py-4">Aucun bureau.</td></tr>
        <?php endif; ?>

        <?php foreach ($liste as $b): ?>
          <tr>
            <td class="table-order-id"><?php echo (int) $b['id_bureau']; ?></td>
            <td><?php echo e($b['nom']); ?></td>
            <td><?php echo e($b['adresse']); ?></td>
            <td><?php echo e($b['ville']); ?></td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="index.php?page=updateBureau&amp;id=<?php echo (int) $b['id_bureau']; ?>" class="table-btn-action" title="Modifier" aria-label="Modifier <?php echo e($b['nom']); ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                <form method="post" action="index.php?page=deleteBureau" class="d-inline"
                      data-confirm="Supprimer ce bureau ?">
                  <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo (int) $b['id_bureau']; ?>">
                  <button type="submit" class="table-btn-action delete border-0" title="Supprimer" aria-label="Supprimer <?php echo e($b['nom']); ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
