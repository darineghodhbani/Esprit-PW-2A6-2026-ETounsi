<header class="navbar-custom">
    <div class="navbar-left">
        <button type="button" class="btn-desktop-toggle align-items-center justify-content-center me-3"
                id="desktop-sidebar-toggle" aria-label="Réduire la navigation" aria-controls="sidebar">
            <i class="bi bi-layout-sidebar" aria-hidden="true"></i>
        </button>
        <button type="button" class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Ouvrir la navigation"
                aria-controls="sidebar" aria-expanded="false">
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>
        <span class="poste-top-label">Espace administrateur <span aria-hidden="true">/</span> Poste</span>
    </div>
    <div class="poste-account-actions">
        <a class="poste-admin-profile" href="../FrontOffice/?page=profil" aria-label="Mon profil">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span><?= e($compteConnecte['nom']) ?><small><?= e($roleConnecte) ?></small></span>
        </a>
        <form method="post" action="../FrontOffice/?page=deconnexion">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <button class="poste-btn poste-btn-quiet" type="submit" aria-label="Se déconnecter"><i class="bi bi-box-arrow-right" aria-hidden="true"></i><span>Déconnexion</span></button>
        </form>
    </div>
</header>
