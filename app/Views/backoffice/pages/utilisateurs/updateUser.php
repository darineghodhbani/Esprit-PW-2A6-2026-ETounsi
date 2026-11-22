<?php
require_once __DIR__ . '/../../../../Controllers/CitoyenController.php';

$compteConnecte = exigerRole(['admin']);
$citoyenC = new CitoyenController();
$title = 'Modifier un utilisateur';
$currentPage = 'admin-utilisateurs';
$section = 'utilisateurs';
$id = posteId($_GET) ?? posteId($_POST);
$old = $id === null ? null : $citoyenC->recupererCitoyen($id);
if (!$old) {
    http_response_code(404);
    $title = 'Utilisateur introuvable';
    $message = 'Ce compte n’existe plus ou le lien est incorrect.';
    require __DIR__ . '/../commun/problem.php';
    return;
}
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = CitoyenController::clean($_POST);
    $old['id'] = $id;
    if (!csrfValide($_POST['csrf'] ?? '')) {
        $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $erreurs = $citoyenC->verifierCitoyen($old, false);
    }
    if (count($erreurs) == 0) {
        $version = $id === $compteConnecte['id'] ? $compteConnecte['version_session'] : null;
        $citoyen = new Citoyen($id, $old['nom'], $old['email'], $old['password'], $old['role'], (int) $old['actif'], $old['prenom'], $old['telephone'], $version);
        $resultat = $citoyenC->modifierCitoyen($citoyen, $id, $old['confirmation']);
        $erreurs = $resultat['erreurs'];
        if (count($erreurs) == 0) {
            if ($id === $compteConnecte['id']) {
                $compte = $resultat['utilisateur'];
                if (!$compte || $compte['actif'] !== 1 || $compte['role'] !== 'admin') {
                    deconnecterUtilisateur();
                    header('Location: ' . frontUrl('connexion'), true, 303);
                    exit;
                }
                connecterUtilisateur($compte);
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'L’utilisateur a été mis à jour.'];
            header('Location: index.php?page=admin-utilisateurs', true, 303);
            exit;
        }
    }
    http_response_code(isset($erreurs['global']) ? 403 : 422);
}

$csrfToken = csrfToken();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Modifier un utilisateur</h1>
    <p class="page-subtitle">Modifiez les informations du compte.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-utilisateurs" class="text-decoration-none text-body">Utilisateurs</a></li>
      <li class="breadcrumb-item active" aria-current="page">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php foreach (['global', 'general'] as $champ): ?>
    <?php if (isset($erreurs[$champ])): ?><p class="alert alert-danger" role="alert"><?php echo e($erreurs[$champ]); ?></p><?php endif; ?>
  <?php endforeach; ?>
  <form method="post" action="index.php?page=admin-utilisateur-update&amp;id=<?php echo e($id); ?>" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <input type="hidden" name="id" value="<?php echo e($id); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="nom" class="form-label-custom">Nom</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'nom'); ?>" id="nom" name="nom" value="<?php echo e($old['nom']); ?>"
               data-validate="required|min:2|max:80" required minlength="2" maxlength="80" autocomplete="family-name"
               <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'nom'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="prenom" class="form-label-custom">Prénom</label>
        <input type="text" class="form-control-custom<?php echo invalidClass($erreurs, 'prenom'); ?>" id="prenom" name="prenom" value="<?php echo e($old['prenom']); ?>"
               data-validate="required|min:2|max:50" required minlength="2" maxlength="50" autocomplete="given-name"
               <?php if (isset($erreurs['prenom'])): ?>aria-invalid="true" aria-describedby="erreur-prenom"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'prenom'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="email" class="form-label-custom">Adresse e-mail</label>
        <input type="email" class="form-control-custom<?php echo invalidClass($erreurs, 'email'); ?>" id="email" name="email" value="<?php echo e($old['email']); ?>"
               data-validate="required|email|max:150" required maxlength="150" autocomplete="email"
               <?php if (isset($erreurs['email'])): ?>aria-invalid="true" aria-describedby="erreur-email"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'email'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="telephone" class="form-label-custom">Téléphone</label>
        <input type="tel" class="form-control-custom<?php echo invalidClass($erreurs, 'telephone'); ?>" id="telephone" name="telephone" value="<?php echo e($old['telephone']); ?>"
               data-validate="required|phone" required placeholder="22123456" minlength="8" maxlength="8" autocomplete="tel"
               <?php if (isset($erreurs['telephone'])): ?>aria-invalid="true" aria-describedby="erreur-telephone"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'telephone'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="role" class="form-label-custom">Rôle</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'role'); ?>" id="role" name="role" data-validate="required" required>
          <?php foreach (CitoyenController::ROLES as $valeur => $libelle): ?>
            <option value="<?php echo e($valeur); ?>" <?php if ((string) $old['role'] === (string) $valeur) echo 'selected'; ?>><?php echo e($libelle); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'role'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="actif" class="form-label-custom">État du compte</label>
        <select class="form-select-custom<?php echo invalidClass($erreurs, 'actif'); ?>" id="actif" name="actif" data-validate="required" required>
          <?php foreach (CitoyenController::ETATS as $valeur => $libelle): ?>
            <option value="<?php echo e($valeur); ?>" <?php if ((string) $old['actif'] === (string) $valeur) echo 'selected'; ?>><?php echo e($libelle); ?></option>
          <?php endforeach; ?>
        </select>
        <?php echo errorFor($erreurs, 'actif'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="mot_de_passe" class="form-label-custom">Nouveau mot de passe</label>
        <input type="password" class="form-control-custom<?php echo invalidClass($erreurs, 'password'); ?>" id="mot_de_passe" name="password"
               data-validate="min:8|max:72" autocomplete="new-password"
               <?php if (isset($erreurs['password'])): ?>aria-invalid="true" aria-describedby="erreur-password"<?php endif; ?>>
        <p class="form-text">Laissez ce champ vide pour conserver le mot de passe actuel.</p>
        <?php echo errorFor($erreurs, 'password'); ?>
      </div>
      <div class="col-12 col-md-6">
        <label for="confirmation" class="form-label-custom">Confirmer le mot de passe</label>
        <input type="password" class="form-control-custom<?php echo invalidClass($erreurs, 'confirmation'); ?>" id="confirmation" name="confirmation"
               data-validate="match:mot_de_passe" autocomplete="new-password"
               <?php if (isset($erreurs['confirmation'])): ?>aria-invalid="true" aria-describedby="erreur-confirmation"<?php endif; ?>>
        <?php echo errorFor($erreurs, 'confirmation'); ?>
      </div>
      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
        <a href="index.php?page=admin-utilisateurs" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
