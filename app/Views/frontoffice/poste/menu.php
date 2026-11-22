<aside class="workspace-sidebar">
<p class="eyebrow"><?= e(t('poste.nom')) ?></p>
<?php if ($utilisateur): ?><div class="sidebar-person"><?= et_icon('user') ?><div><strong><?= e(trim(($utilisateur['prenom'] ?? '') . ' ' . $utilisateur['nom'])) ?></strong><small><?= e($utilisateur['email']) ?></small></div></div><?php endif; ?>
<nav aria-label="<?= e(t('poste.services_postaux')) ?>">
<?php foreach (['bureaux-poste' => t('poste.bureaux'), 'suivi-colis' => t('poste.suivre_colis'), 'declarer-envoi' => t('poste.declarer_envoi'), 'mes-colis' => t('poste.mes_colis')] as $route => $libelle): ?>
<a href="index.php?page=<?= e($route) ?>" <?= $currentPage === $route ? 'aria-current="page"' : '' ?>><?= et_icon('mail') ?> <?= e($libelle) ?></a>
<?php endforeach; ?>
</nav><a class="sidebar-logout" href="index.php?page=module&amp;id=poste"><?= et_icon('arrow-left') ?> <?= e(t('poste.retour')) ?></a>
</aside>
