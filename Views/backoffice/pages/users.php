<div class="page-header">
  <div>
    <h1 class="page-title">Utilisateurs</h1>
    <p class="page-subtitle">Comptes des citoyens et des administrateurs.</p>
  </div>
  <a href="admin.php?page=user-form" class="btn-custom btn-custom-primary text-decoration-none">
    <i class="bi bi-person-plus"></i> Ajouter un utilisateur
  </a>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Total</span><i class="bi bi-people text-muted-green"></i></div>
      <div class="stat-value"><?php echo (int) $stats['total']; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Citoyens</span><i class="bi bi-person text-muted-green"></i></div>
      <div class="stat-value"><?php echo (int) $stats['users']; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Administrateurs</span><i class="bi bi-shield-check text-muted-green"></i></div>
      <div class="stat-value"><?php echo (int) $stats['admins']; ?></div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card card-stat">
      <div class="card-header"><span class="stat-label">Comptes bloqués</span><i class="bi bi-slash-circle text-muted-green"></i></div>
      <div class="stat-value"><?php echo (int) $stats['bloques']; ?></div>
    </div>
  </div>
</div>

<div class="table-card-custom">

  <form method="get" action="admin.php" class="table-header-control flex-wrap gap-2">
    <input type="hidden" name="page" value="users">
    <div class="table-search-box">
      <i class="bi bi-search table-search-icon"></i>
      <input type="text" name="q" class="table-search-input" value="<?php echo e($search); ?>" maxlength="100"
             placeholder="Rechercher par nom, prénom ou email...">
    </div>
    <div class="table-filter-group">
      <select name="role" class="form-select-custom" aria-label="Filtrer par rôle">
        <option value="">Tous les rôles</option>
        <?php foreach (UserController::ROLES as $key => $label): ?>
          <option value="<?php echo e($key); ?>" <?php echo $role === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
        <?php endforeach; ?>
      </select>
      <select name="statut" class="form-select-custom" aria-label="Filtrer par statut">
        <option value="">Tous les statuts</option>
        <?php foreach (UserController::STATUTS as $key => $label): ?>
          <option value="<?php echo e($key); ?>" <?php echo $statut === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-custom btn-custom-primary btn-custom-sm">Filtrer</button>
      <a href="admin.php?page=users" class="btn-custom btn-custom-light btn-custom-sm text-decoration-none">Effacer</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom complet</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Rôle</th>
          <th>Statut</th>
          <th>Inscription</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$list): ?>
          <tr><td colspan="8" class="text-center py-4">Aucun utilisateur trouvé.</td></tr>
        <?php endif; ?>

        <?php foreach ($list as $u): ?>
          <?php $isSelf = $u->getId() === (int) $_SESSION['user_id']; ?>
          <tr>
            <td class="table-order-id"><?php echo $u->getId(); ?></td>
            <td><?php echo e($u->getNomComplet()); ?><?php echo $isSelf ? ' <small class="text-muted">(vous)</small>' : ''; ?></td>
            <td><?php echo e($u->getEmail()); ?></td>
            <td><?php echo e($u->getTelephone()); ?></td>
            <td>
              <span class="badge-table <?php echo $u->getRole() === 'admin' ? 'pending' : 'success'; ?>">
                <?php echo e(UserController::ROLES[$u->getRole()] ?? $u->getRole()); ?>
              </span>
            </td>
            <td>
              <span class="badge-table <?php echo $u->getStatut() === 'actif' ? 'success' : 'failed'; ?>">
                <?php echo e(UserController::STATUTS[$u->getStatut()] ?? $u->getStatut()); ?>
              </span>
            </td>
            <td><?php echo e(date('d/m/Y', strtotime($u->getCreatedAt()))); ?></td>
            <td>
              <div class="d-flex justify-content-center gap-1">
                <a href="admin.php?page=user-show&amp;id=<?php echo $u->getId(); ?>" class="table-btn-action" title="Voir"><i class="bi bi-eye"></i></a>
                <a href="admin.php?page=user-form&amp;id=<?php echo $u->getId(); ?>" class="table-btn-action" title="Modifier"><i class="bi bi-pencil"></i></a>
                <?php if (!$isSelf): ?>

                  <form method="post" action="admin.php?page=user-toggle" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                    <input type="hidden" name="id" value="<?php echo $u->getId(); ?>">
                    <button type="submit" class="table-btn-action border-0"
                            title="<?php echo $u->getStatut() === 'actif' ? 'Bloquer' : 'Débloquer'; ?>">
                      <i class="bi <?php echo $u->getStatut() === 'actif' ? 'bi-lock' : 'bi-unlock'; ?>"></i>
                    </button>
                  </form>
                  <form method="post" action="admin.php?page=user-delete" class="d-inline"
                        data-confirm="Supprimer définitivement <?php echo e($u->getNomComplet()); ?> ?">
                    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                    <input type="hidden" name="id" value="<?php echo $u->getId(); ?>">
                    <button type="submit" class="table-btn-action delete border-0" title="Supprimer"><i class="bi bi-trash"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
