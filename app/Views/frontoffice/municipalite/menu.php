<aside class="workspace-sidebar">
  <p class="eyebrow">Municipalité</p>
  <?php if($utilisateur): ?><div class="sidebar-person"><?= et_icon('user') ?><div><strong><?= e(trim(($utilisateur['prenom']??'').' '.$utilisateur['nom'])) ?></strong><small><?= e($utilisateur['email']) ?></small></div></div><?php endif; ?>
  <nav aria-label="Services municipaux">
    <a href="index.php?page=municipalite-services" <?= in_array($currentPage,['municipalite-services','municipalite-rendez-vous'],true)?'aria-current="page"':'' ?>><?= et_icon('landmark') ?> Démarches municipales</a>
    <a href="index.php?page=municipalite-rendez-vous" <?= $currentPage==='municipalite-rendez-vous'?'aria-current="page"':'' ?>><?= et_icon('calendar') ?> Prendre un rendez-vous</a>
    <a href="index.php?page=municipalite-documents" <?= $currentPage==='municipalite-documents'?'aria-current="page"':'' ?>><?= et_icon('file') ?> Mes documents</a>
    <a href="index.php?page=municipalite-suivi" <?= $currentPage==='municipalite-suivi'?'aria-current="page"':'' ?>><?= et_icon('file') ?> Mes demandes et notifications</a>
  </nav>
  <a class="sidebar-logout" href="index.php?page=module&amp;id=municipalite"><?= et_icon('arrow-left') ?> Retour à la Municipalité</a>
</aside>
