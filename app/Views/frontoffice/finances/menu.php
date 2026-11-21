<aside class="workspace-sidebar">
    <p class="eyebrow">Recette des Finances</p>
    <div class="sidebar-person">
        <?php echo et_icon('user'); ?>
        <div>
            <strong><?php echo e($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></strong>
            <small>Espace citoyen</small>
        </div>
    </div>
    <nav aria-label="Recette des Finances">
        <a href="index.php?page=declarer-propriete" <?php if ($currentPage == 'declarer-propriete') echo 'aria-current="page"'; ?>>
            <?php echo et_icon('landmark'); ?> Déclarer une propriété
        </a>
        <a href="index.php?page=payer-impot" <?php if ($currentPage == 'payer-impot') echo 'aria-current="page"'; ?>>
            <?php echo et_icon('receipt'); ?> Payer mes impôts
        </a>
        <a href="index.php?page=mes-recus" <?php if ($currentPage == 'mes-recus') echo 'aria-current="page"'; ?>>
            <?php echo et_icon('file'); ?> Mes reçus
        </a>
    </nav>
    <a class="sidebar-logout" href="index.php?page=module&id=finances">
        <?php echo et_icon('arrow-left'); ?> Retour à la Recette des Finances
    </a>
</aside>
