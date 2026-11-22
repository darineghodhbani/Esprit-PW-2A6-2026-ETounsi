<aside class="workspace-sidebar">
<p class="eyebrow"><?= e(tr('La Poste', 'البريد')) ?></p>
<?php if ($utilisateur): ?><div class="sidebar-person"><?= et_icon('user') ?><div><strong><?= e(trim(($utilisateur['prenom'] ?? '') . ' ' . $utilisateur['nom'])) ?></strong><small><?= e($utilisateur['email']) ?></small></div></div><?php endif; ?>
<nav aria-label="<?= e(tr('Services postaux', 'الخدمات البريدية')) ?>">
<?php foreach (['bureaux-poste' => tr('Bureaux de poste', 'مكاتب البريد'), 'suivi-colis' => tr('Suivre un colis', 'تتبّع طرد'), 'declarer-envoi' => tr('Déclarer un envoi', 'التصريح بإرسال طرد'), 'mes-colis' => tr('Mes colis', 'طرودي')] as $route => $libelle): ?>
<a href="index.php?page=<?= e($route) ?>" <?= $currentPage === $route ? 'aria-current="page"' : '' ?>><?= et_icon('mail') ?> <?= e($libelle) ?></a>
<?php endforeach; ?>
</nav><a class="sidebar-logout" href="index.php?page=module&amp;id=poste"><?= et_icon('arrow-left') ?> <?= e(tr('Retour à la Poste', 'الرجوع إلى البريد')) ?></a>
</aside>
