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
