<aside class="workspace-sidebar">
    <p class="eyebrow"><?= e(tr('Transport', 'النقل')) ?></p>
    <?php if ($utilisateur): ?><div class="sidebar-person"><?= et_icon('user') ?><div><strong><?= e(trim(($utilisateur['prenom'] ?? '') . ' ' . $utilisateur['nom'])) ?></strong><small><?= e($utilisateur['email']) ?></small></div></div><?php endif; ?>
    <nav aria-label="<?= e(tr('Services de transport', 'خدمات النقل')) ?>">
        <a href="index.php?page=lignes-transport" <?= $currentPage === 'lignes-transport' ? 'aria-current="page"' : '' ?>><?= et_icon('bus-front') ?> <?= e(t('service.transport.lignes')) ?></a>
        <a href="index.php?page=stations-transport" <?= $currentPage === 'stations-transport' ? 'aria-current="page"' : '' ?>><?= et_icon('map-pin') ?> <?= e(t('service.transport.stations')) ?></a>
    </nav>
    <a class="sidebar-logout" href="index.php?page=module&amp;id=transport"><?= et_icon('arrow-left') ?> <?= e(tr('Retour au bureau de transport', 'العودة إلى مكتب النقل')) ?></a>
</aside>
