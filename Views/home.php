<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
</head>

<body class="et-page">
    <?php require_once __DIR__ . '/includes/spinner.php'; ?>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <section id="accueil" class="container-xxl pt-4">
        <div class="et-hero">
            <img src="img/bureaux/e-tounsi-hero.jpg" alt="Rue tunisienne avec drapeaux, palmiers et tramway" class="et-hero-img">
            <div class="et-hero-veil"></div>
            <div class="et-hero-content">
                <span class="et-badge-glass"><i class="bi bi-stars me-2"></i>Pensé pour les citoyens tunisiens</span>
                <h1 class="et-title text-white mt-4">Vos services publics, en toute simplicité.</h1>
                <p class="et-hero-text mt-4">Transport, santé, démarches et services du quotidien : trouvez ce qu'il vous faut, au même endroit.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="#services" class="btn et-btn-red">Découvrir les services <i class="fa fa-arrow-right ms-2"></i></a>
                    <a href="index.php?page=administratif" class="btn et-btn-white"><i class="fa fa-landmark text-primary me-2"></i>Recette des Finances</a>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="container-xxl py-5 my-4">
        <div class="row align-items-end g-3">
            <div class="col-lg-7">
                <h2 class="et-title">De quoi avez-vous besoin ?</h2>
                <p class="text-muted mt-3 mb-0">Cinq services du quotidien, réunis pour vous aider à vous repérer et à préparer vos démarches.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <p class="text-muted fw-medium mb-0"><?php echo count($services); ?> services</p>
            </div>
        </div>

        <ul class="et-doors mt-5">
            <?php foreach ($services as $service): ?>
                <li class="et-door">
                    <a href="<?php echo e($service['lien']); ?>" aria-label="Entrer : <?php echo e($service['nom']); ?>">
                        <img src="img/bureaux/<?php echo e($service['image']); ?>" alt="" loading="lazy">
                        <span class="et-door-veil"></span>
                        <span class="et-door-text">
                            <i class="fa <?php echo e($service['icone']); ?> fa-lg"></i>
                            <span class="d-block fw-bold mt-2"><?php echo e($service['nom']); ?></span>
                            <?php if ($service['lien'] === '#services'): ?>
                                <span class="et-door-more">Bientôt disponible</span>
                            <?php else: ?>
                                <span class="et-door-more">Ouvrir la porte <i class="fa fa-arrow-right ms-1"></i></span>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section id="etapes" class="bg-white border-top border-bottom">
        <div class="container-xxl py-5">
            <h2 class="et-title text-center py-3">Vos démarches, en 3 étapes</h2>
            <div class="row g-5 mt-2 mb-4 text-center">
                <div class="col-md-4">
                    <span class="et-step-icon"><i class="fa fa-mouse-pointer fa-lg"></i><span class="et-step-num">1</span></span>
                    <h5 class="mt-4">Choisissez un service</h5>
                    <p class="text-muted small mx-auto et-step-text">Transport, municipalité, CNAM, poste ou finances : partez de votre besoin.</p>
                </div>
                <div class="col-md-4">
                    <span class="et-step-icon"><i class="fa fa-file-alt fa-lg"></i><span class="et-step-num">2</span></span>
                    <h5 class="mt-4">Retrouvez les informations utiles</h5>
                    <p class="text-muted small mx-auto et-step-text">Consultez la page du service et les actions disponibles.</p>
                </div>
                <div class="col-md-4">
                    <span class="et-step-icon"><i class="fa fa-clipboard-check fa-lg"></i><span class="et-step-num">3</span></span>
                    <h5 class="mt-4">Faites votre démarche</h5>
                    <p class="text-muted small mx-auto et-step-text">Connectez-vous, remplissez le formulaire et suivez votre demande.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="container-xxl pt-5">
        <div class="et-help d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-4">
            <div>
                <h3 class="et-title mb-2">Une question ? On vous guide.</h3>
                <p class="text-muted mb-0">Écrivez-nous à contact@etounsi.tn ou consultez les questions fréquentes.</p>
            </div>
            <a href="#faq" class="btn et-btn-red"><i class="fa fa-comment me-2"></i>Questions fréquentes</a>
        </div>
    </section>

    <section id="faq" class="container py-5 my-4" style="max-width: 760px;">
        <h2 class="et-title text-center mb-4">Questions fréquentes</h2>
        <div class="accordion et-faq" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="q1">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#r1">Comment trouver le bon service ?</button>
                </h2>
                <div id="r1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Parcourez les cinq cartes de la section « Nos services » et cliquez sur celle qui vous concerne.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="q2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#r2">Comment payer un impôt ?</button>
                </h2>
                <div id="r2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Dans « Recette des Finances », cliquez sur « Payer » puis rendez-vous au bureau de poste. L'impôt passe à « Payé » après validation par l'administration.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="q3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#r3">E-Tounsi est-il un site officiel ?</button>
                </h2>
                <div id="r3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Non. E-Tounsi est un projet étudiant. Pour toute démarche, référez-vous aux administrations concernées.</div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>

</html>
