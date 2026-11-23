<section class="poste-hero" aria-labelledby="documents-title">
  <span class="poste-hero-label">E-Tounsi · Municipalité</span>
  <h1 class="page-title" id="documents-title">Mes documents municipaux</h1>
  <p>Retrouvez l’état des documents liés à vos demandes et imprimez le récépissé correspondant.</p>
  <a class="btn-custom btn-custom-primary" href="index.php?page=municipalite-services">Consulter les services</a>
</section>

<?php if (!$documents): ?>
  <p class="content-panel mt-4">Aucun document municipal n’est encore associé à vos demandes.</p>
<?php else: ?>
  <div class="row g-3 mt-1">
    <?php foreach ($documents as $document): ?>
      <div class="col-12 col-lg-6">
        <article class="content-panel h-100">
          <p class="eyebrow mb-1">Demande DM-<?= str_pad((string)$document['id_demande'],5,'0',STR_PAD_LEFT) ?></p>
          <h2 class="h5"><?= e($document['type_document']) ?></h2>
          <p class="mb-2">État du document :
            <strong><?= e(str_replace('_',' ',ucfirst($document['document_statut']))) ?></strong>
          </p>
          <?php if ($document['document_statut']==='pret_a_retirer'): ?>
            <p class="alert alert-success">Votre document est prêt. Retirez-le au guichet municipal.</p>
          <?php elseif ($document['document_statut']==='en_attente_demande'): ?>
            <p class="text-secondary">Votre demande a été transmise ; le traitement municipal n’a pas encore commencé.</p>
          <?php elseif ($document['document_statut']==='en_preparation'): ?>
            <p class="text-secondary">Le document est en cours de préparation par la municipalité.</p>
          <?php elseif ($document['document_statut']==='rejete'): ?>
            <p class="text-secondary">Consultez le statut et les notifications de votre demande pour les détails.</p>
          <?php endif; ?>
          <a class="button button-light" href="index.php?page=municipalite-recu&amp;id=<?= (int)$document['id_demande'] ?>">Voir / imprimer le récépissé</a>
        </article>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
