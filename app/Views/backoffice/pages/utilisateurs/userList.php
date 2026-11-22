<?php
// Vue : affichage seulement. Les données viennent de CitoyenController::listBack().
?>
<div class="page-header poste-page-header">
    <div>
        <h1 class="page-title"><?php echo e($title); ?></h1>
        <p class="page-subtitle">Gérez les comptes, les rôles et l’accès à l’application.</p>
    </div>
    <div class="poste-table-actions"><a class="btn-custom btn-custom-primary" href="?espace=admin&amp;page=addUser"><i class="bi bi-plus-lg" aria-hidden="true"></i> Ajouter un utilisateur</a></div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-md-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Total des comptes</span>
        <span class="stat-icone stat-gris"><i class="bi bi-people-fill"></i></span>
      </div>
      <div class="stat-value"><?php echo $nbCitoyens + $nbAdmins; ?></div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Citoyens</span>
        <span class="stat-icone stat-jaune"><i class="bi bi-person-fill"></i></span>
      </div>
      <div class="stat-value"><?php echo $nbCitoyens; ?></div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-stat">
      <div class="card-header">
        <span class="stat-label">Administrateurs</span>
        <span class="stat-icone stat-rouge"><i class="bi bi-shield-lock-fill"></i></span>
      </div>
      <div class="stat-value"><?php echo $nbAdmins; ?></div>
    </div>
  </div>
</div>
<section class="table-card-custom" aria-label="Comptes utilisateurs">
    <form method="get" class="table-header-control poste-filter" action="" novalidate>
        <input type="hidden" name="espace" value="admin">
        <input type="hidden" name="page" value="utilisateurs">
        <div class="poste-filter-field">
            <label for="q">Rechercher un utilisateur</label>
            <input class="form-control-custom" id="q" name="q" type="search" maxlength="120" value="<?php echo e($recherche); ?>" placeholder="Nom, prénom, e-mail ou téléphone">
        </div>
        <button class="btn-custom btn-custom-light" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Rechercher</button>
        <?php if ($recherche !== ''): ?><a class="btn-custom btn-custom-light" href="?espace=admin&amp;page=userList">Effacer</a><?php endif; ?>
    </form>
    <?php if ($liste === []): ?>
        <p class="poste-empty">Aucun utilisateur trouvé. Essayez un autre nom ou une autre adresse e-mail.</p>
    <?php else: ?>
        <div class="table-responsive" tabindex="0" role="region" aria-label="Tableau des utilisateurs">
            <table class="table-custom">
                <caption class="visually-hidden">Utilisateurs de l’application</caption>
                <thead><tr><th scope="col">Nom</th><th scope="col">Prénom</th><th scope="col">Adresse e-mail</th><th scope="col">Téléphone</th><th scope="col">Rôle</th><th scope="col">État</th><th scope="col">Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($liste as $citoyen): ?>
                        <tr>
                            <th scope="row"><?php echo e($citoyen['nom']); ?></th>
                            <td><?php echo e($citoyen['prenom']); ?></td>
                            <td><?php echo e($citoyen['email']); ?></td>
                            <td><?php echo e($citoyen['telephone']); ?></td>
                            <td><?php echo e($roles[$citoyen['role']] ?? $citoyen['role']); ?></td>
                            <td>
                                <?php if ((int) $citoyen['actif'] === 1): ?>
                                    <span class="poste-status poste-status-delivered">Actif</span>
                                <?php else: ?>
                                    <span class="poste-status poste-status-cancelled">Désactivé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="poste-table-actions">
                                    <a class="btn-custom btn-custom-light btn-custom-sm" href="?espace=admin&amp;page=updateUser&amp;id=<?php echo e($citoyen['id']); ?>" aria-label="Modifier <?php echo e($citoyen['nom']); ?>">Modifier</a>
                                    <form method="post" action="index.php?page=deleteUser" data-confirm="Supprimer cet utilisateur ?">
                                        <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
                                        <input type="hidden" name="id" value="<?php echo e($citoyen['id']); ?>">
                                        <button class="btn-custom btn-custom-danger btn-custom-sm" type="submit" aria-label="Supprimer <?php echo e($citoyen['nom']); ?>"><i class="bi bi-trash3" aria-hidden="true"></i> Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
