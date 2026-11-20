<div class="page-header">
  <div>
    <h1 class="page-title"><?php echo e($user->getNomComplet()); ?></h1>
    <p class="page-subtitle">Détail du compte n°<?php echo $user->getId(); ?></p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="admin.php?page=users" class="text-decoration-none text-muted-green">Utilisateurs</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Détail</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <div class="row g-3">
    <div class="col-12 col-md-6"><span class="form-label-custom">Nom</span><div><?php echo e($user->getNom()); ?></div></div>
    <div class="col-12 col-md-6"><span class="form-label-custom">Prénom</span><div><?php echo e($user->getPrenom()); ?></div></div>
    <div class="col-12 col-md-6"><span class="form-label-custom">Email</span><div><?php echo e($user->getEmail()); ?></div></div>
    <div class="col-12 col-md-6"><span class="form-label-custom">Téléphone</span><div><?php echo e($user->getTelephone()); ?></div></div>
    <div class="col-12 col-md-6">
      <span class="form-label-custom">Rôle</span>
      <div><span class="badge-table <?php echo $user->getRole() === 'admin' ? 'pending' : 'success'; ?>"><?php echo e(UserController::ROLES[$user->getRole()] ?? $user->getRole()); ?></span></div>
    </div>
    <div class="col-12 col-md-6">
      <span class="form-label-custom">Statut</span>
      <div><span class="badge-table <?php echo $user->getStatut() === 'actif' ? 'success' : 'failed'; ?>"><?php echo e(UserController::STATUTS[$user->getStatut()] ?? $user->getStatut()); ?></span></div>
    </div>
    <div class="col-12 col-md-6"><span class="form-label-custom">Date d'inscription</span><div><?php echo e(date('d/m/Y à H:i', strtotime($user->getCreatedAt()))); ?></div></div>
  </div>
  <div class="d-flex gap-2 mt-4">
    <a href="admin.php?page=user-form&amp;id=<?php echo $user->getId(); ?>" class="btn-custom btn-custom-primary text-decoration-none">Modifier</a>
    <a href="admin.php?page=users" class="btn-custom btn-custom-light text-decoration-none">Retour à la liste</a>
  </div>
</div>
