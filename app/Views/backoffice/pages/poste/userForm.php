<?php $modification = !empty($old['id']); ?>
<a class="poste-back" href="?espace=admin&amp;page=userList"><i class="bi bi-arrow-left" aria-hidden="true"></i> Gestion des utilisateurs</a>
<div class="page-header poste-page-header"><h1 class="page-title"><?php echo e($title); ?></h1></div>
<section class="poste-form-card" aria-label="Informations de l’utilisateur">
    <?php if ($erreurs !== []): ?>
        <div class="alert alert-danger" role="alert">
            <p class="mb-1">Vérifiez les informations du compte.</p>
            <ul class="mb-0">
                <?php foreach ($erreurs as $message): ?><li><?php echo e($message); ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="?espace=admin&amp;page=<?php echo $modification ? 'updateUser' : 'addUser'; ?>" novalidate data-poste-save>
        <input type="hidden" name="csrf" value="<?php echo e($csrf); ?>">
        <?php if ($modification): ?><input type="hidden" name="id" value="<?php echo e($old['id']); ?>"><?php endif; ?>

        <div class="poste-field">
            <label for="nom">Nom complet</label>
            <input class="form-control-custom<?php echo isset($erreurs['nom']) ? ' is-invalid' : ''; ?>" type="text" id="nom" name="nom" required minlength="2" maxlength="80" autocomplete="name" value="<?php echo e($old['nom']); ?>"
                <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
            <?php if (isset($erreurs['nom'])): ?><p class="invalid-feedback" id="erreur-nom"><?php echo e($erreurs['nom']); ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="email">Adresse e-mail</label>
            <input class="form-control-custom<?php echo isset($erreurs['email']) ? ' is-invalid' : ''; ?>" type="email" id="email" name="email" required maxlength="150" autocomplete="email" value="<?php echo e($old['email']); ?>"
                <?php if (isset($erreurs['email'])): ?>aria-invalid="true" aria-describedby="erreur-email"<?php endif; ?>>
            <?php if (isset($erreurs['email'])): ?><p class="invalid-feedback" id="erreur-email"><?php echo e($erreurs['email']); ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="role">Rôle</label>
            <select class="form-select form-control-custom<?php echo isset($erreurs['role']) ? ' is-invalid' : ''; ?>" id="role" name="role" required
                <?php if (isset($erreurs['role'])): ?>aria-invalid="true" aria-describedby="erreur-role"<?php endif; ?>>
                <?php foreach ($roles as $valeur => $libelle): ?>
                    <option value="<?php echo e($valeur); ?>" <?php echo $old['role'] === $valeur ? 'selected' : ''; ?>><?php echo e($libelle); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($erreurs['role'])): ?><p class="invalid-feedback" id="erreur-role"><?php echo e($erreurs['role']); ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="actif">État du compte</label>
            <select class="form-select form-control-custom<?php echo isset($erreurs['actif']) ? ' is-invalid' : ''; ?>" id="actif" name="actif" required
                <?php if (isset($erreurs['actif'])): ?>aria-invalid="true" aria-describedby="erreur-actif"<?php endif; ?>>
                <option value="1" <?php echo (string) $old['actif'] === '1' ? 'selected' : ''; ?>>Actif</option>
                <option value="0" <?php echo (string) $old['actif'] === '0' ? 'selected' : ''; ?>>Désactivé</option>
            </select>
            <?php if (isset($erreurs['actif'])): ?><p class="invalid-feedback" id="erreur-actif"><?php echo e($erreurs['actif']); ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="mot_de_passe"><?php echo $modification ? 'Nouveau mot de passe' : 'Mot de passe'; ?></label>
            <div class="poste-password">
                <input class="form-control-custom<?php echo isset($erreurs['password']) ? ' is-invalid' : ''; ?>" type="password" id="mot_de_passe" name="password" minlength="8" autocomplete="new-password" <?php echo $modification ? '' : 'required'; ?>
                    aria-describedby="aide-mot-de-passe<?php echo isset($erreurs['password']) ? ' erreur-mot-de-passe' : ''; ?>" <?php echo isset($erreurs['password']) ? 'aria-invalid="true"' : ''; ?>>
                <button class="btn-custom btn-custom-light" type="button" data-password-toggle="mot_de_passe" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
            </div>
            <p class="form-text" id="aide-mot-de-passe">Au moins 8 caractères et au maximum 72 octets.<?php echo $modification ? ' Laissez ce champ vide pour conserver le mot de passe actuel.' : ''; ?></p>
            <?php if (isset($erreurs['password'])): ?><p class="invalid-feedback d-block" id="erreur-mot-de-passe"><?php echo e($erreurs['password']); ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="confirmation">Confirmer le mot de passe</label>
            <div class="poste-password">
                <input class="form-control-custom<?php echo isset($erreurs['confirmation']) ? ' is-invalid' : ''; ?>" type="password" id="confirmation" name="confirmation" minlength="8" autocomplete="new-password" <?php echo $modification ? '' : 'required'; ?>
                    <?php if (isset($erreurs['confirmation'])): ?>aria-invalid="true" aria-describedby="erreur-confirmation"<?php endif; ?>>
                <button class="btn-custom btn-custom-light" type="button" data-password-toggle="confirmation" aria-label="Afficher la confirmation" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
            </div>
            <?php if (isset($erreurs['confirmation'])): ?><p class="invalid-feedback d-block" id="erreur-confirmation"><?php echo e($erreurs['confirmation']); ?></p><?php endif; ?>
        </div>

        <div class="poste-table-actions">
            <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
            <a class="btn-custom btn-custom-light" href="?espace=admin&amp;page=userList">Annuler</a>
        </div>
    </form>
</section>
