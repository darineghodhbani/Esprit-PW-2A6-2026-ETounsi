<?php
$message = $data['message'];
?>
<section class="poste-form-card">
    <h1 class="page-title"><?= e($title) ?></h1>
    <p><?= e($message) ?></p>
    <a class="btn-custom btn-custom-light" href="?espace=admin&amp;page=<?= e($section) ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i> Retour</a>
</section>
