<section class="poste-hero" aria-labelledby="municipalite-title">
  <span class="poste-hero-label">E-Tounsi · Municipalité</span><h1 class="page-title" id="municipalite-title">Démarches municipales</h1>
  <p>Choisissez un service pour déposer une demande ou prendre rendez-vous. Retrouvez ensuite le récépissé, le paiement et les notifications dans votre suivi.</p>
  <div class="d-flex flex-wrap gap-2"><a class="btn-custom btn-custom-primary" href="index.php?page=municipalite-suivi">Mes demandes et notifications</a></div>
</section>
<section class="module-page" style="--module-accent:#365F90" aria-label="Services municipaux">
<div class="planned-services">
<?php foreach($services as $i=>$service): ?><a class="service-link" href="index.php?page=municipalite-demande&amp;id=<?= (int)$service['id_service'] ?>"><article>
  <span class="step-number"><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></span><div>
    <h3><?= e($service['libelle']) ?></h3><p><?= e($service['description']) ?></p>
    <p><strong>Frais :</strong> <?= e(number_format((float)$service['frais'],3,',',' ')) ?> DT</p><p><strong>À préparer :</strong> <?= e($service['pieces_requises']) ?></p>
    <span class="badge badge-success"><?= $service['mode_traitement']==='rendez_vous'?'Prendre rendez-vous':'Formulaire · retrait au guichet' ?></span>
  </div><span class="card-arrow" aria-hidden="true">→</span>
</article></a><?php endforeach; ?>
</div></section>
