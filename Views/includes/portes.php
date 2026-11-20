    <?php if (isset($_GET['porte'])): ?>
        <div class="et-entree" aria-hidden="true">
            <img src="img/bureaux/<?php echo e($porteImage); ?>" alt="" class="et-entree-img">
            <div class="et-hero-veil"></div>
            <h2 class="et-entree-titre"><?php echo e($porteTitre); ?></h2>
            <div class="et-porte et-porte-gauche"></div>
            <div class="et-porte et-porte-droite"></div>
        </div>
    <?php endif; ?>
