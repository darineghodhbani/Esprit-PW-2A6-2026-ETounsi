    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php?page=home" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>E-Tounsi</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php?page=home" class="nav-item nav-link <?php echo ($currentPage == 'home') ? 'active' : ''; ?>">Accueil</a>
                <a href="index.php?page=about" class="nav-item nav-link <?php echo ($currentPage == 'about') ? 'active' : ''; ?>">À propos</a>
                <a href="index.php?page=courses" class="nav-item nav-link <?php echo ($currentPage == 'courses') ? 'active' : ''; ?>">Services</a>
                <a href="modules/poste/FrontOffice/" class="nav-item nav-link">Poste</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['team', 'testimonial', '404']) ? 'active' : ''; ?>" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="index.php?page=team" class="dropdown-item">Notre équipe</a>
                        <a href="index.php?page=testimonial" class="dropdown-item">Témoignages</a>
                        <a href="index.php?page=404" class="dropdown-item">Page 404</a>
                    </div>
                </div>
                <a href="index.php?page=contact" class="nav-item nav-link <?php echo ($currentPage == 'contact') ? 'active' : ''; ?>">Contact</a>
            </div>
            <a href="modules/poste/FrontOffice/?page=connexion" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Se connecter<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->
