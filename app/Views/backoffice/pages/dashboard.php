<div class="admin-banniere mb-4">
  <div class="admin-banniere-texte">
    <span class="admin-badge"><i class="bi bi-shield-check me-1"></i> Espace <?php echo $_SESSION['role']; ?></span>
    <h1 class="page-title text-white mt-3 mb-2">Bonjour <?php echo e($_SESSION['prenom']); ?> !</h1>
    <p class="mb-0">Bienvenue dans le back office d'E-Tounsi.</p>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-md-6 col-xl-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Transport</span>
        <span class="stat-icone stat-rouge"><i class="bi bi-bus-front"></i></span>
      </div>
      <div class="d-flex gap-2 mt-3">
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-lignes">Lignes</a>
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-stations">Stations</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Municipalité</span>
        <span class="stat-icone stat-rouge"><i class="bi bi-building"></i></span>
      </div>
      <div class="d-flex gap-2 mt-3">
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-services">Services</a>
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-demandes-municipales">Demandes</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">CNAM</span>
        <span class="stat-icone stat-rouge"><i class="bi bi-heart-pulse"></i></span>
      </div>
      <div class="d-flex gap-2 mt-3">
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-assures">Assurés</a>
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-demandes-cnam">Remboursements</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Poste</span>
        <span class="stat-icone stat-rouge"><i class="bi bi-envelope"></i></span>
      </div>
      <div class="d-flex gap-2 mt-3">
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-bureaux">Bureaux</a>
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-colis">Colis</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Finances</span>
        <span class="stat-icone stat-rouge"><i class="bi bi-receipt"></i></span>
      </div>
      <div class="d-flex gap-2 mt-3">
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-proprietes">Propriétés</a>
        <a class="btn-custom btn-custom-light text-decoration-none" href="index.php?page=admin-impots">Impôts</a>
      </div>
    </div>
  </div>
</div>

<section class="poste-admin" aria-label="Activité du module Poste">
<?php
$stats = $poste['stats'];
$parcels = $poste['parcels'];
$statusCounts = $poste['statusCounts'];
$section = 'dashboard';
?>
<section class="poste-hero" aria-labelledby="tableau-de-bord">
    <span class="poste-hero-label">Administration · Poste</span>
    <h2 id="tableau-de-bord" class="page-title">Activité Poste</h2>
    <p>Bienvenue dans votre espace de gestion des colis et des bureaux de poste.</p>
    <a class="btn-custom btn-custom-primary" href="?espace=admin&amp;page=addColis"><i class="bi bi-plus-lg" aria-hidden="true"></i> Enregistrer un colis</a>
</section>

<div class="poste-metrics" aria-label="Chiffres du module Poste">
    <?php foreach ($stats as $stat): ?>
        <div class="poste-metric">
            <span class="poste-metric-icon"><i class="bi bi-<?= e($stat['icon']) ?>" aria-hidden="true"></i></span>
            <div><strong><?= e($stat['value']) ?></strong><span><?= e($stat['label']) ?></span></div>
        </div>
    <?php endforeach; ?>
</div>

<section class="poste-distribution" aria-labelledby="repartition">
    <h2 id="repartition">Statuts des colis</h2>
    <dl>
        <?php foreach ($statusCounts as $status => $count): ?>
            <div><dt><?= e($status) ?></dt><dd><?= e($count) ?></dd></div>
        <?php endforeach; ?>
    </dl>
</section>

<section class="table-card-custom" aria-labelledby="derniers-colis">
    <div class="table-header-control poste-card-header">
        <h2 id="derniers-colis">Derniers colis</h2>
        <a class="btn-custom btn-custom-light" href="?espace=admin&amp;page=colis">Voir tous les colis <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </div>
    <?php if ($parcels === []): ?>
        <p class="poste-empty">Aucun colis enregistré.</p>
    <?php else: ?>
        <?php require __DIR__ . '/../partials/poste-colis.php'; ?>
    <?php endif; ?>
</section>

</section>
