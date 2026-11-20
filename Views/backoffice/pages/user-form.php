<div class="page-header">
  <div>
    <h1 class="page-title"><?php echo e($title); ?></h1>
    <p class="page-subtitle">Tous les champs sont obligatoires<?php echo $id ? ', sauf le mot de passe' : ''; ?>.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="admin.php?page=users" class="text-decoration-none text-muted-green">Utilisateurs</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page"><?php echo $id ? 'Modifier' : 'Ajouter'; ?></li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($errors['global'])): ?>
    <div class="alert-custom alert-custom-danger"><?php echo e($errors['global']); ?></div>
  <?php endif; ?>

  <form method="post" action="admin.php?page=user-form<?php echo $id ? '&amp;id=' . $id : ''; ?>" novalidate data-validate-form>
    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="nom" class="form-label-custom">Nom</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'nom'); ?>" id="nom" name="nom"
               value="<?php echo e($old['nom']); ?>" data-validate="required|name">
        <?php echo errorFor($errors, 'nom'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="prenom" class="form-label-custom">Prénom</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'prenom'); ?>" id="prenom" name="prenom"
               value="<?php echo e($old['prenom']); ?>" data-validate="required|name">
        <?php echo errorFor($errors, 'prenom'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="email" class="form-label-custom">Email</label>
        <input type="email" class="form-control-custom<?php echo invalidClass($errors, 'email'); ?>" id="email" name="email"
               value="<?php echo e($old['email']); ?>" data-validate="required|email">
        <?php echo errorFor($errors, 'email'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="telephone" class="form-label-custom">Téléphone</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($errors, 'telephone'); ?>" id="telephone" name="telephone"
               value="<?php echo e($old['telephone']); ?>" placeholder="22123456" data-validate="required|phone">
        <?php echo errorFor($errors, 'telephone'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="role" class="form-label-custom">Rôle</label>
        <select class="form-select-custom<?php echo invalidClass($errors, 'role'); ?>" id="role" name="role" data-validate="required" <?php echo $isSelf ? 'disabled' : ''; ?>>
          <?php foreach (UserController::ROLES as $key => $label): ?>
            <option value="<?php echo e($key); ?>" <?php echo $old['role'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($errors, 'role'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="statut" class="form-label-custom">Statut</label>
        <select class="form-select-custom<?php echo invalidClass($errors, 'statut'); ?>" id="statut" name="statut" data-validate="required" <?php echo $isSelf ? 'disabled' : ''; ?>>
          <?php foreach (UserController::STATUTS as $key => $label): ?>
            <option value="<?php echo e($key); ?>" <?php echo $old['statut'] === $key ? 'selected' : ''; ?>><?php echo e($label); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($errors, 'statut'); ?>
      </div>
      <?php if ($isSelf): ?>

        <input type="hidden" name="role" value="admin">
        <input type="hidden" name="statut" value="actif">
        <div class="col-12"><small class="text-muted">Vous ne pouvez pas modifier votre propre rôle ni votre statut.</small></div>
      <?php endif; ?>

      <div class="col-12 col-md-6">
        <label for="password" class="form-label-custom">
          Mot de passe<?php echo $id ? ' (laisser vide pour ne pas le changer)' : ''; ?>
        </label>
        <input type="password" class="form-control-custom<?php echo invalidClass($errors, 'password'); ?>" id="password" name="password"
               data-validate="<?php echo $id ? 'password' : 'required|password'; ?>">
        <?php echo errorFor($errors, 'password'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="password_confirm" class="form-label-custom">Confirmer le mot de passe</label>
        <input type="password" class="form-control-custom<?php echo invalidClass($errors, 'password_confirm'); ?>" id="password_confirm" name="password_confirm"
               data-validate="<?php echo $id ? 'match:password' : 'required|match:password'; ?>">
        <?php echo errorFor($errors, 'password_confirm'); ?>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn-custom btn-custom-primary" type="submit"><?php echo $id ? 'Enregistrer' : 'Ajouter'; ?></button>
        <a href="admin.php?page=users" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
