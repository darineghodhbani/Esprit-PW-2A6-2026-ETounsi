<?php
require_once __DIR__ . '/../../../../Controllers/CitoyenController.php';

CitoyenController::requireAdmin();

$citoyenC = new CitoyenController();
$erreurs = [];
$old = CitoyenController::clean([]);
$old['role'] = 'citoyen';
$old['actif'] = '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = CitoyenController::clean($_POST);
    if (!csrfValide($_POST['csrf'] ?? '')) {
        $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $erreurs = $citoyenC->verifierCitoyen($old);
    }
    if (count($erreurs) == 0) {
        $citoyen = new Citoyen(null, $old['nom'], $old['email'], $old['password'], $old['role'], (int) $old['actif']);
        $resultat = $citoyenC->ajouterCitoyen($citoyen, $old['confirmation']);
        $erreurs = $resultat['erreurs'];
        if (count($erreurs) == 0) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été ajouté.'];
            header('Location: index.php?page=admin-utilisateurs', true, 303);
            exit;
        }
    }
    http_response_code(isset($erreurs['global']) ? 403 : 422);
}

$roles = CitoyenController::ROLES;
$title = 'Ajouter un utilisateur';
$currentPage = 'admin-utilisateurs';
$section = 'utilisateurs';
$csrfToken = csrfToken();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Ajouter un utilisateur</h1>
    <p class="page-subtitle">Renseignez les informations du formulaire.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-utilisateurs" class="text-decoration-none text-body">Utilisateurs</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Ajouter</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <?php if (isset($erreurs['general'])): ?><p class="alert alert-danger" role="alert"><?php echo e($erreurs['general']); ?></p><?php endif; ?>
  <form method="post" action="index.php?page=admin-utilisateur-add" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="nom" class="form-label-custom">Nom complet</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'nom'); ?>" id="nom" name="nom"
               value="<?php echo e($old['nom']); ?>" data-validate="required|min:2|max:80" required minlength="2" maxlength="80" autocomplete="name"
               <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'nom'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="email" class="form-label-custom">Adresse e-mail</label>
        <input type="email" class="form-control-custom<?php echo invalidClass($erreurs, 'email'); ?>" id="email" name="email"
               value="<?php echo e($old['email']); ?>" data-validate="required|email|max:150" required maxlength="150" autocomplete="email"
               <?php if (isset($erreurs['email'])): ?>aria-invalid="true" aria-describedby="erreur-email"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'email'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="role" class="form-label-custom">Rôle</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'role'); ?>" id="role" name="role" data-validate="required" required
                <?php if (isset($erreurs['role'])): ?>aria-invalid="true" aria-describedby="erreur-role"<?php endif; ?>>
          <option value="">-- Choisir --</option>
          <?php foreach ($roles as $valeur => $libelle): ?>
            <option value="<?php echo e($valeur); ?>" <?php if ($old['role'] === $valeur) echo 'selected'; ?>><?php echo e($libelle); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'role'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="actif" class="form-label-custom">État du compte</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'actif'); ?>" id="actif" name="actif" data-validate="required" required
                <?php if (isset($erreurs['actif'])): ?>aria-invalid="true" aria-describedby="erreur-actif"<?php endif; ?>>
          <option value="">-- Choisir --</option>
          <option value="1" <?php if ((string) $old['actif'] === '1') echo 'selected'; ?>>Actif</option>
          <option value="0" <?php if ((string) $old['actif'] === '0') echo 'selected'; ?>>Désactivé</option>
        </select>
        <?php echo errorFor($erreurs, 'actif'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="mot_de_passe" class="form-label-custom">Mot de passe</label>
        <div class="input-group poste-password">
          <input type="password" class="form-control-custom<?php echo invalidClass($erreurs, 'password'); ?>" id="mot_de_passe" name="password" data-validate="required|min:8|max:72" required autocomplete="new-password"
                 <?php if (isset($erreurs['password'])): ?>aria-invalid="true" aria-describedby="erreur-password"<?php endif; ?>>
          <button class="btn-custom btn-custom-light" type="button" data-password-toggle="mot_de_passe" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
        </div>
        <p class="form-text">Au moins 8 caractères et au maximum 72 octets.</p>
        <?php echo errorFor($erreurs, 'password'); ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="confirmation" class="form-label-custom">Confirmer le mot de passe</label>
        <div class="input-group poste-password">
          <input type="password" class="form-control-custom<?php echo invalidClass($erreurs, 'confirmation'); ?>" id="confirmation" name="confirmation" data-validate="required|match:mot_de_passe" required autocomplete="new-password"
                 <?php if (isset($erreurs['confirmation'])): ?>aria-invalid="true" aria-describedby="erreur-confirmation"<?php endif; ?>>
          <button class="btn-custom btn-custom-light" type="button" data-password-toggle="confirmation" aria-label="Afficher le mot de passe" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
        </div>
        <?php echo errorFor($erreurs, 'confirmation'); ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Ajouter</button>
        <a href="index.php?page=admin-utilisateurs" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
