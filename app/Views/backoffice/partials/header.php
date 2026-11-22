<header class="navbar-custom">
    <div class="navbar-left">
        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
                id="desktop-sidebar-toggle" aria-label="Réduire le menu">
            <i class="bi bi-chevron-bar-left"></i>
        </button>
        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Ouvrir le menu">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="navbar-actions">
        <a href="index.php?page=home" class="btn-voir-site me-3"><i class="bi bi-globe me-2"></i>Voir le site</a>

        <div class="dropdown">
            <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false" id="profile-dropdown" aria-label="Menu du compte">
                <span class="avatar-cercle"><i class="bi bi-person-fill"></i></span>
                <span class="navbar-profile-name d-none d-md-inline"><?php echo e($_SESSION['prenom'] ?: $_SESSION['nom']); ?></span>
                <i class="bi bi-chevron-down navbar-profile-caret"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="profile-dropdown">
                <li class="dropdown-header">Bonjour <?php echo e($_SESSION['prenom'] ?: $_SESSION['nom']); ?> !</li>
                <li><a class="dropdown-item" href="index.php?page=profile"><i class="bi bi-person"></i> Mon profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><form method="post" action="index.php?page=logout" class="logout-inline"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Se déconnecter</button></form></li>
            </ul>
        </div>
    </div>
</header>
