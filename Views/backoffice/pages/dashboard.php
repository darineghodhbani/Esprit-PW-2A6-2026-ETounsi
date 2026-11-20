<div class="admin-banniere mb-4">
  <div class="admin-banniere-texte">
    <span class="admin-badge"><i class="bi bi-shield-check me-1"></i> Espace administrateur</span>
    <h1 class="page-title text-white mt-3 mb-2">Bonjour <?php echo e($_SESSION['prenom']); ?> !</h1>
    <p class="mb-0">Voici la vue d’ensemble des comptes E-Tounsi.</p>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-md-4">
    <a href="admin.php?page=users" class="text-decoration-none">
      <div class="card card-stat">
        <div class="card-header">
          <span class="stat-label">Comptes</span>
          <span class="stat-icone stat-rouge"><i class="bi bi-people-fill"></i></span>
        </div>
        <div class="stat-value"><?php echo (int) $userStats['total']; ?></div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-4">
    <a href="admin.php?page=users&amp;role=user" class="text-decoration-none">
      <div class="card card-stat">
        <div class="card-header">
          <span class="stat-label">Citoyens inscrits</span>
          <span class="stat-icone stat-jaune"><i class="bi bi-person-fill"></i></span>
        </div>
        <div class="stat-value"><?php echo (int) $userStats['users']; ?></div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-4">
    <a href="admin.php?page=users&amp;statut=bloque" class="text-decoration-none">
      <div class="card card-stat">
        <div class="card-header">
          <span class="stat-label">Comptes bloqués</span>
          <span class="stat-icone stat-gris"><i class="bi bi-slash-circle-fill"></i></span>
        </div>
        <div class="stat-value"><?php echo (int) $userStats['bloques']; ?></div>
      </div>
    </a>
  </div>
</div>
