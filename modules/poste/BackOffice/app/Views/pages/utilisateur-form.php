<?php
$utilisateur = $data['utilisateur'];
$erreurs = $data['erreurs'];
$roles = $data['roles'];
$modification = !empty($utilisateur['id']);
?>
<a class="poste-back" href="?page=utilisateurs"><i class="bi bi-arrow-left" aria-hidden="true"></i> Gestion des utilisateurs</a>
<div class="page-header poste-page-header"><h1 class="page-title"><?= e($title) ?></h1></div>
<section class="poste-form-card" aria-label="Informations de l’utilisateur">
    <?php if ($erreurs !== []): ?>
        <div class="alert alert-danger" role="alert">
            <p class="mb-1">Vérifiez les informations du compte.</p>
            <ul class="mb-0">
                <?php foreach ($erreurs as $message): ?><li><?= e($message) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="?page=utilisateur-save" novalidate data-poste-save>
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <?php if ($modification): ?><input type="hidden" name="id" value="<?= e($utilisateur['id']) ?>"><?php endif; ?>

        <div class="poste-field">
            <label for="nom">Nom complet</label>
            <input class="form-control-custom<?= isset($erreurs['nom']) ? ' is-invalid' : '' ?>" type="text" id="nom" name="nom" required minlength="2" maxlength="80" autocomplete="name" value="<?= e($utilisateur['nom']) ?>"
                <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
            <?php if (isset($erreurs['nom'])): ?><p class="invalid-feedback" id="erreur-nom"><?= e($erreurs['nom']) ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="email">Adresse e-mail</label>
            <input class="form-control-custom<?= isset($erreurs['email']) ? ' is-invalid' : '' ?>" type="email" id="email" name="email" required maxlength="150" autocomplete="email" value="<?= e($utilisateur['email']) ?>"
                <?php if (isset($erreurs['email'])): ?>aria-invalid="true" aria-describedby="erreur-email"<?php endif; ?>>
            <?php if (isset($erreurs['email'])): ?><p class="invalid-feedback" id="erreur-email"><?= e($erreurs['email']) ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="role">Rôle</label>
            <select class="form-select form-control-custom<?= isset($erreurs['role']) ? ' is-invalid' : '' ?>" id="role" name="role" required
                <?php if (isset($erreurs['role'])): ?>aria-invalid="true" aria-describedby="erreur-role"<?php endif; ?>>
                <?php foreach ($roles as $valeur => $libelle): ?>
                    <option value="<?= e($valeur) ?>" <?= $utilisateur['role'] === $valeur ? 'selected' : '' ?>><?= e($libelle) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($erreurs['role'])): ?><p class="invalid-feedback" id="erreur-role"><?= e($erreurs['role']) ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="actif">État du compte</label>
            <select class="form-select form-control-custom<?= isset($erreurs['actif']) ? ' is-invalid' : '' ?>" id="actif" name="actif" required
                <?php if (isset($erreurs['actif'])): ?>aria-invalid="true" aria-describedby="erreur-actif"<?php endif; ?>>
                <option value="1" <?= (string) $utilisateur['actif'] === '1' ? 'selected' : '' ?>>Actif</option>
                <option value="0" <?= (string) $utilisateur['actif'] === '0' ? 'selected' : '' ?>>Désactivé</option>
            </select>
            <?php if (isset($erreurs['actif'])): ?><p class="invalid-feedback" id="erreur-actif"><?= e($erreurs['actif']) ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="mot_de_passe"><?= $modification ? 'Nouveau mot de passe' : 'Mot de passe' ?></label>
            <div class="poste-password">
                <input class="form-control-custom<?= isset($erreurs['password']) ? ' is-invalid' : '' ?>" type="password" id="mot_de_passe" name="password" minlength="8" autocomplete="new-password" <?= $modification ? '' : 'required' ?>
                    aria-describedby="aide-mot-de-passe<?= isset($erreurs['password']) ? ' erreur-mot-de-passe' : '' ?>" <?= isset($erreurs['password']) ? 'aria-invalid="true"' : '' ?>>
                <button class="poste-btn poste-btn-outline" type="button" data-password-toggle="mot_de_passe" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
            </div>
            <p class="form-text" id="aide-mot-de-passe">Au moins 8 caractères et au maximum 72 octets.<?= $modification ? ' Laissez ce champ vide pour conserver le mot de passe actuel.' : '' ?></p>
            <?php if (isset($erreurs['password'])): ?><p class="invalid-feedback d-block" id="erreur-mot-de-passe"><?= e($erreurs['password']) ?></p><?php endif; ?>
        </div>

        <div class="poste-field">
            <label for="confirmation">Confirmer le mot de passe</label>
            <div class="poste-password">
                <input class="form-control-custom<?= isset($erreurs['confirmation']) ? ' is-invalid' : '' ?>" type="password" id="confirmation" name="confirmation" minlength="8" autocomplete="new-password" <?= $modification ? '' : 'required' ?>
                    <?php if (isset($erreurs['confirmation'])): ?>aria-invalid="true" aria-describedby="erreur-confirmation"<?php endif; ?>>
                <button class="poste-btn poste-btn-outline" type="button" data-password-toggle="confirmation" aria-label="Afficher la confirmation" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
            </div>
            <?php if (isset($erreurs['confirmation'])): ?><p class="invalid-feedback d-block" id="erreur-confirmation"><?= e($erreurs['confirmation']) ?></p><?php endif; ?>
        </div>

        <div class="poste-table-actions">
            <button class="poste-btn poste-btn-primary" type="submit">Enregistrer</button>
            <a class="poste-btn poste-btn-quiet" href="?page=utilisateurs">Annuler</a>
        </div>
    </form>
</section>
