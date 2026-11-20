    <header class="navbar-custom">
      <div class="navbar-left">

        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
          id="desktop-sidebar-toggle" aria-label="Réduire le menu">
          <i class="bi bi-chevron-bar-left"></i>
        </button>

        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Ouvrir le menu">
          <i class="bi bi-list"></i>
        </button>

        <div class="dropdown ms-2">
          <button class="btn-quick-action dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
            id="quick-actions-dropdown">
            <i class="bi bi-plus-lg"></i>
            <span>Créer</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-quick-action" aria-labelledby="quick-actions-dropdown">
            <li class="dropdown-header">Ajouter</li>
            <li><a class="dropdown-item" href="admin.php?page=propriete-form"><i class="bi bi-house"></i> Nouvelle propriété</a></li>
            <li><a class="dropdown-item" href="admin.php?page=impot-form"><i class="bi bi-receipt"></i> Nouvel impôt</a></li>
            <li><a class="dropdown-item" href="admin.php?page=user-form"><i class="bi bi-person-plus"></i> Nouvel utilisateur</a></li>
          </ul>
        </div>
      </div>

      <div class="navbar-actions">
        <a href="index.php?page=home" class="btn-voir-site me-3"><i class="bi bi-globe me-2"></i>Voir le site</a>

        <div class="dropdown">
          <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false" id="profile-dropdown">
            <span class="avatar-cercle"><i class="bi bi-person-fill"></i></span>
            <span class="navbar-profile-name d-none d-md-inline"><?php echo e($_SESSION['prenom']); ?></span>
            <i class="bi bi-chevron-down navbar-profile-caret"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="profile-dropdown">
            <li class="dropdown-header">Bonjour <?php echo e($_SESSION['prenom']); ?> !</li>
            <li><a class="dropdown-item" href="index.php?page=profil"><i class="bi bi-person"></i> Mon profil</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="index.php?page=logout&amp;token=<?php echo e($csrfToken); ?>"><i class="bi bi-box-arrow-right"></i>
                Se déconnecter</a></li>
          </ul>
        </div>
      </div>
    </header>
