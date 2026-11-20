  <div class="sidebar-wrapper" id="sidebar">

    <a href="admin.php?page=dashboard" class="sidebar-brand">
      <?php echo logoHtml('36px', '<i class="bi bi-asterisk"></i>'); ?>
      <span>E-Tounsi</span>
    </a>

    <div class="flex-grow-1 overflow-y-auto">
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Menu</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="admin.php?page=dashboard" class="sidebar-menu-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>" title="Tableau de bord">
              <i class="bi bi-grid-fill"></i>
              <span>Tableau de bord</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Recette des Finances</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="admin.php?page=proprietes" class="sidebar-menu-link <?php echo ($currentPage == 'proprietes') ? 'active' : ''; ?>" title="Propriétés">
              <i class="bi bi-house"></i>
              <span>Propriétés</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="admin.php?page=impots" class="sidebar-menu-link <?php echo ($currentPage == 'impots') ? 'active' : ''; ?>" title="Impôts">
              <i class="bi bi-receipt"></i>
              <span>Impôts</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Gestion des utilisateurs</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="admin.php?page=users" class="sidebar-menu-link <?php echo ($currentPage == 'users') ? 'active' : ''; ?>" title="Utilisateurs">
              <i class="bi bi-people"></i>
              <span>Utilisateurs</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Autres</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="index.php?page=home" class="sidebar-menu-link <?php echo ($currentPage == 'site') ? 'active' : ''; ?>" title="Voir le site">
              <i class="bi bi-globe"></i>
              <span>Voir le site</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="sidebar-profile">
      <span class="avatar-cercle"><i class="bi bi-person-fill"></i></span>
      <div class="sidebar-profile-info">
        <div class="sidebar-profile-name"><?php echo e($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></div>
        <div class="sidebar-profile-email"><?php echo e($_SESSION['email']); ?></div>
      </div>
    </div>
  </div>
