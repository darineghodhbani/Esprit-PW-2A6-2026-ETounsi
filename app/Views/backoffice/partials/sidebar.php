<?php

$role = $_SESSION['role'] ?? 'citoyen';

$menu = [
    ['section' => 'Général',      'module' => null, 'items' => [
        ['page' => 'dashboard', 'label' => 'Tableau de bord', 'icone' => 'bi-grid-fill'],
        ['page' => 'admin-utilisateurs', 'label' => 'Utilisateurs', 'icone' => 'bi-people'],
        ['page' => 'admin-audit', 'label' => 'Journal d’audit', 'icone' => 'bi-clock-history'],
    ]],
    ['section' => 'Transport',    'module' => 'transport', 'items' => [
        ['page' => 'admin-lignes', 'label' => 'Lignes', 'icone' => 'bi-bus-front'],
        ['page' => 'admin-stations', 'label' => 'Stations', 'icone' => 'bi-geo-alt'],
        ['page' => 'admin-horaires', 'label' => 'Horaires', 'icone' => 'bi-clock'],
    ]],
    ['section' => 'Municipalité', 'module' => 'municipalite', 'items' => [
        ['page' => 'admin-services', 'label' => 'Services', 'icone' => 'bi-building'],
        ['page' => 'admin-demandes-municipales', 'label' => 'Demandes', 'icone' => 'bi-inbox'],
    ]],
    ['section' => 'CNAM',         'module' => 'cnam', 'items' => [
        ['page' => 'admin-assures', 'label' => 'Assurés', 'icone' => 'bi-heart-pulse'],
        ['page' => 'admin-beneficiaires', 'label' => 'Bénéficiaires', 'icone' => 'bi-people'],
        ['page' => 'admin-demandes-cnam', 'label' => 'Demandes de remboursement', 'icone' => 'bi-file-earmark-medical'],
    ]],
    ['section' => 'Poste',        'module' => 'poste', 'items' => [
        ['page' => 'admin-bureaux', 'label' => 'Bureaux postaux', 'icone' => 'bi-envelope'],
        ['page' => 'admin-colis', 'label' => 'Colis', 'icone' => 'bi-box-seam'],
    ]],
    ['section' => 'Finances',     'module' => 'finances', 'items' => [
        ['page' => 'admin-proprietes', 'label' => 'Propriétés', 'icone' => 'bi-house'],
        ['page' => 'admin-impots', 'label' => 'Impôts', 'icone' => 'bi-receipt'],
    ]],
];
?>
<div class="sidebar-wrapper" id="sidebar">

    <a href="index.php?page=dashboard" class="sidebar-brand">
        <img src="assets/frontoffice/img/brand/e-tounsi-mark.svg" alt="" style="height: 36px; width: auto;">
        <span>E-Tounsi</span>
    </a>

    <div class="flex-grow-1 overflow-y-auto">
        <?php foreach ($menu as $groupe): ?>
                        <div class="sidebar-menu-section">
                <div class="sidebar-menu-title"><?php echo $groupe['section']; ?></div>
                <ul class="sidebar-menu-list">
                    <?php foreach ($groupe['items'] as $lien): ?>
                        <li class="sidebar-menu-item">
                            <a href="index.php?page=<?php echo $lien['page']; ?>"
                               class="sidebar-menu-link <?php if ($currentPage == $lien['page']) echo 'active'; ?>"
                               title="<?php echo $lien['label']; ?>">
                                <i class="bi <?php echo $lien['icone']; ?>"></i>
                                <span><?php echo $lien['label']; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="sidebar-profile">
        <span class="avatar-cercle"><i class="bi bi-person-fill"></i></span>
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name">
                <?php echo e($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>
            </div>
            <div class="sidebar-profile-email"><?php echo $role; ?></div>
        </div>
    </div>
</div>
