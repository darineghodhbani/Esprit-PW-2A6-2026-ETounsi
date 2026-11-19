<?php
$items = [
    'dashboard' => ['label' => 'Tableau de bord', 'icon' => 'grid'],
    'colis' => ['label' => 'Gestion des colis', 'icon' => 'box'],
    'bureaux' => ['label' => 'Bureaux de poste', 'icon' => 'geo-alt'],
    'utilisateurs' => ['label' => 'Utilisateurs', 'icon' => 'people'],
];
?>
<aside class="sidebar-wrapper" id="sidebar" aria-label="Menu du module Poste">
    <a href="?espace=admin&amp;page=dashboard" class="sidebar-brand" title="E-Tounsi · Administration">
        <img class="poste-brand-logo" src="public/assets/images/logo-etounsi.png" width="54" height="76" alt="E‑Tounsi · إي تونسي">
        <span><small>Administration</small></span>
    </a>
    <nav class="flex-grow-1 overflow-y-auto" aria-label="Navigation principale">
        <div class="sidebar-menu-section">
            <p class="sidebar-menu-title">Module Poste</p>
            <ul class="sidebar-menu-list">
                <?php foreach ($items as $key => $item): ?>
                    <li class="sidebar-menu-item">
                        <a href="?espace=admin&amp;page=<?= e($key) ?>" class="sidebar-menu-link<?= $section === $key ? ' active' : '' ?>"
                           title="<?= e($item['label']) ?>" <?= $section === $key ? 'aria-current="page"' : '' ?>>
                            <i class="bi bi-<?= e($item['icon']) ?>" aria-hidden="true"></i>
                            <span><?= e($item['label']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
    <nav class="sidebar-menu-section sidebar-exit" aria-label="Espace citoyen">
        <a href="<?= e(frontUrl()) ?>" class="sidebar-menu-link" title="Espace citoyen">
            <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
            <span>Espace citoyen</span>
        </a>
    </nav>
</aside>
