<div class="page-header">
  <div>
    <h1 class="page-title">Modifier un utilisateur</h1>
    <p class="page-subtitle">Renseignez les informations du formulaire.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-utilisateurs" class="text-decoration-none text-body">Utilisateurs</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($erreurs['general'])): ?><p class="alert alert-danger" role="alert"><?php echo e($erreurs['general']); ?></p><?php endif; ?>
  <form method="post" action="index.php?page=admin-utilisateur-update&amp;id=<?php echo e($old['id']); ?>" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e(csrfToken()); ?>">
    <input type="hidden" name="id" value="<?php echo e($old['id']); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="nom" class="form-label-custom">Nom complet</label>
        <input type="text" class="form-control-custom<?php if (isset($erreurs['nom'])) echo ' is-invalid'; ?>" id="nom" name="nom"
               value="<?php echo e($old['nom']); ?>" data-validate="required|min:2|max:80" required minlength="2" maxlength="80" autocomplete="name"
               <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
        <?php if (isset($erreurs['nom'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-nom" role="alert"><?php echo e($erreurs['nom']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="email" class="form-label-custom">Adresse e-mail</label>
        <input type="email" class="form-control-custom<?php if (isset($erreurs['email'])) echo ' is-invalid'; ?>" id="email" name="email"
               value="<?php echo e($old['email']); ?>" data-validate="required|email|max:150" required maxlength="150" autocomplete="email"
               <?php if (isset($erreurs['email'])): ?>aria-invalid="true" aria-describedby="erreur-email"<?php endif; ?>>
        <?php if (isset($erreurs['email'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-email" role="alert"><?php echo e($erreurs['email']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="role" class="form-label-custom">Rôle</label>
        <select class="form-select-custom<?php if (isset($erreurs['role'])) echo ' is-invalid'; ?>" id="role" name="role" data-validate="required" required
                <?php if (isset($erreurs['role'])): ?>aria-invalid="true" aria-describedby="erreur-role"<?php endif; ?>>
          <option value="">-- Choisir --</option>
          <?php foreach ($roles as $valeur => $libelle): ?>
            <option value="<?php echo e($valeur); ?>" <?php if ($old['role'] === $valeur) echo 'selected'; ?>><?php echo e($libelle); ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['role'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-role" role="alert"><?php echo e($erreurs['role']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="actif" class="form-label-custom">État du compte</label>
        <select class="form-select-custom<?php if (isset($erreurs['actif'])) echo ' is-invalid'; ?>" id="actif" name="actif" data-validate="required" required
                <?php if (isset($erreurs['actif'])): ?>aria-invalid="true" aria-describedby="erreur-actif"<?php endif; ?>>
          <option value="">-- Choisir --</option>
          <option value="1" <?php if ((string) $old['actif'] === '1') echo 'selected'; ?>>Actif</option>
          <option value="0" <?php if ((string) $old['actif'] === '0') echo 'selected'; ?>>Désactivé</option>
        </select>
        <?php if (isset($erreurs['actif'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-actif" role="alert"><?php echo e($erreurs['actif']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="mot_de_passe" class="form-label-custom">Nouveau mot de passe</label>
        <div class="input-group poste-password">
          <input type="password" class="form-control-custom<?php if (isset($erreurs['password'])) echo ' is-invalid'; ?>" id="mot_de_passe" name="password" data-validate="min:8|max:72"  autocomplete="new-password"
                 <?php if (isset($erreurs['password'])): ?>aria-invalid="true" aria-describedby="erreur-password"<?php endif; ?>>
          <button class="btn-custom btn-custom-light" type="button" data-password-toggle="mot_de_passe" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
        </div>
        <p class="form-text">Au moins 8 caractères et au maximum 72 octets. Laissez ce champ vide pour conserver le mot de passe actuel.</p>
        <?php if (isset($erreurs['password'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-password" role="alert"><?php echo e($erreurs['password']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="confirmation" class="form-label-custom">Confirmer le mot de passe</label>
        <div class="input-group poste-password">
          <input type="password" class="form-control-custom<?php if (isset($erreurs['confirmation'])) echo ' is-invalid'; ?>" id="confirmation" name="confirmation" data-validate="match:mot_de_passe"  autocomplete="new-password"
                 <?php if (isset($erreurs['confirmation'])): ?>aria-invalid="true" aria-describedby="erreur-confirmation"<?php endif; ?>>
          <button class="btn-custom btn-custom-light" type="button" data-password-toggle="confirmation" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
        </div>
        <?php if (isset($erreurs['confirmation'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-confirmation" role="alert"><?php echo e($erreurs['confirmation']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
        <a href="index.php?page=admin-utilisateurs" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
