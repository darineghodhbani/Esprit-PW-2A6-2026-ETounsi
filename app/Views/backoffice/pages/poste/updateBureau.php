<?php
require_once __DIR__ . '/../../../../Controllers/CitoyenController.php';
require_once __DIR__ . '/../../../../Controllers/BureauPostalController.php';
CitoyenController::requireAdmin();

$bureauC = new BureauPostalController();
$title = 'Modifier un bureau';
$currentPage = 'admin-bureaux';
$section = 'bureaux';

$id = posteId($_GET) ?? posteId($_POST);
$old = $id === null ? null : $bureauC->recupererBureau($id);
if (!$old) {
    http_response_code(404);
    $title = 'Bureau introuvable';
    $message = 'Cet élément n’existe plus ou le lien est incorrect.';
    require __DIR__ . '/../commun/problem.php';
    return;
}
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = BureauPostalController::clean($_POST);
    $old['id_bureau'] = $id;
    if (!csrfValide($_POST['csrf'] ?? '')) {
        $erreurs['global'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $erreurs = $bureauC->verifierBureau($old);
    }
    if (count($erreurs) == 0) {
        $bureau = new BureauPostal($id, $old['nom'], $old['adresse'], $old['ville']);
        if (!$bureauC->modifierBureau($bureau, $id)) {
            http_response_code(404);
            $title = 'Bureau introuvable';
            $message = 'Cet élément n’existe plus ou le lien est incorrect.';
            require __DIR__ . '/../commun/problem.php';
            return;
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Le bureau a été mis à jour.'];
        header('Location: index.php?page=admin-bureaux', true, 303);
        exit;
    }
    http_response_code(isset($erreurs['global']) ? 403 : 422);
}

$csrfToken = csrfToken();
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Modifier un bureau</h1>
    <p class="page-subtitle">Tous les champs sont obligatoires.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=admin-bureaux" class="text-decoration-none text-body">Bureaux de poste</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <?php if (isset($erreurs['global'])): ?>
    <div class="alert alert-danger" role="alert"><?php echo e($erreurs['global']); ?></div>
  <?php endif; ?>
  <form method="post" action="index.php?page=admin-bureau-update&amp;id=<?php echo e($id); ?>" novalidate data-validate-form data-poste-save>
    <input type="hidden" name="csrf" value="<?php echo e($csrfToken); ?>">
    <input type="hidden" name="id" value="<?php echo e($id); ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="nom" class="form-label-custom">Nom du bureau</label>
        <input type="text" class="form-control-custom<?php if (isset($erreurs['nom'])) echo ' is-invalid'; ?>" id="nom" name="nom"
               value="<?php echo e($old['nom']); ?>" data-validate="required|max:100" required maxlength="100"
               <?php if (isset($erreurs['nom'])): ?>aria-invalid="true" aria-describedby="erreur-nom"<?php endif; ?>>
        <?php if (isset($erreurs['nom'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-nom" role="alert"><?php echo e($erreurs['nom']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="adresse" class="form-label-custom">Adresse</label>
        <input type="text" class="form-control-custom<?php if (isset($erreurs['adresse'])) echo ' is-invalid'; ?>" id="adresse" name="adresse"
               value="<?php echo e($old['adresse']); ?>" data-validate="required|max:200" required maxlength="200"
               <?php if (isset($erreurs['adresse'])): ?>aria-invalid="true" aria-describedby="erreur-adresse"<?php endif; ?>>
        <?php if (isset($erreurs['adresse'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-adresse" role="alert"><?php echo e($erreurs['adresse']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 col-md-6">
        <label for="ville" class="form-label-custom">Ville</label>
        <input type="text" class="form-control-custom<?php if (isset($erreurs['ville'])) echo ' is-invalid'; ?>" id="ville" name="ville"
               value="<?php echo e($old['ville']); ?>" data-validate="required|max:80" required maxlength="80"
               <?php if (isset($erreurs['ville'])): ?>aria-invalid="true" aria-describedby="erreur-ville"<?php endif; ?>>
        <?php if (isset($erreurs['ville'])): ?>
          <div class="invalid-feedback d-block server-error" id="erreur-ville" role="alert"><?php echo e($erreurs['ville']); ?></div>
        <?php endif; ?>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button class="btn-custom btn-custom-primary" type="submit">Enregistrer</button>
        <a href="index.php?page=admin-bureaux" class="btn-custom btn-custom-light text-decoration-none">Annuler</a>
      </div>
    </div>
  </form>
</div>
