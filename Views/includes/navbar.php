    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php?page=home" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary d-flex align-items-center"><?php echo logoHtml(); ?>E-Tounsi</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php?page=home" class="nav-item nav-link <?php echo ($currentPage == 'home') ? 'active' : ''; ?>">Accueil</a>
                <a href="index.php?page=home#services" class="nav-item nav-link">Nos services</a>
                <a href="index.php?page=home#etapes" class="nav-item nav-link">Comment ça marche</a>
                <a href="index.php?page=home#faq" class="nav-item nav-link">FAQ</a>
                <a href="index.php?page=administratif" class="nav-item nav-link <?php echo ($currentPage == 'administratif') ? 'active' : ''; ?>">Recette des Finances</a>
            </div>
            <?php if (AuthController::isAuthenticated()): ?>

                <div class="nav-item dropdown">
                    <a href="#" class="btn btn-primary py-4 px-lg-5 dropdown-toggle <?php echo ($currentPage == 'profil') ? 'active' : ''; ?>" data-bs-toggle="dropdown">
                        <i class="fa fa-user me-2"></i><?php echo e($_SESSION['prenom']); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end fade-down m-0">
                        <a href="index.php?page=profil" class="dropdown-item">Mon profil</a>
                        <?php if (AuthController::hasRole('admin')): ?>
                            <a href="admin.php" class="dropdown-item">Back Office</a>
                        <?php endif; ?>
                        <a href="index.php?page=logout&amp;token=<?php echo e($csrfToken); ?>" class="dropdown-item text-danger">Se déconnecter</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="index.php?page=login&amp;porte=1" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Se connecter<i class="fa fa-arrow-right ms-3"></i></a>
                <a href="index.php?page=login&amp;porte=1" class="nav-item nav-link d-lg-none">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>

<?php require __DIR__ . '/flash.php'; ?>
