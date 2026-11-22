<aside class="workspace-sidebar">
    <p class="eyebrow"><?= e(tr('CNAM','الصندوق الوطني للتأمين على المرض')) ?></p>
    <?php if ($utilisateur): ?><div class="sidebar-person"><?= et_icon('user') ?><div><strong><?= e(trim(($utilisateur['prenom']??'').' '.$utilisateur['nom'])) ?></strong><small><?= e($utilisateur['email']) ?></small></div></div><?php endif; ?>
    <nav aria-label="<?= e(tr('Services CNAM','خدمات الصندوق')) ?>">
        <?php foreach (['mon-dossier'=>tr('Mon dossier','ملفي'),'mes-beneficiaires'=>tr('Mes bénéficiaires','المستفيدون'),'mes-remboursements'=>tr('Mes remboursements','استرجاع مصاريفي')] as $route=>$libelle): ?>
            <a href="index.php?page=<?= e($route) ?>" <?= $currentPage===$route?'aria-current="page"':'' ?>><?= et_icon('heart-pulse') ?> <?= e($libelle) ?></a>
        <?php endforeach; ?>
    </nav>
    <a class="sidebar-logout" href="index.php?page=module&amp;id=cnam"><?= et_icon('arrow-left') ?> <?= e(tr('Retour à la CNAM','العودة إلى الصندوق')) ?></a>
</aside>
