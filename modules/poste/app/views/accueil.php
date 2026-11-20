<?php
$etServiceKey = 'poste';
$etService = etServices()['poste'];
require __DIR__ . '/shared/office.php';
?>
<section class="et-container et-section" aria-labelledby="demarches">
    <div class="et-section-heading"><span class="et-eyebrow"><?= etE(tr('Les services postaux', 'الخدمات البريدية')) ?></span><h2 id="demarches"><?= etE(tr('Une démarche à effectuer ?', 'عندك إجراء تقوم بيه؟')) ?></h2></div>
    <?php require __DIR__ . '/shared/services-poste.php'; ?>
</section>
