<!doctype html>
<html lang="<?= etE($language) ?>" dir="<?= $language === 'ar' ? 'rtl' : 'ltr' ?>">
<head><?php require __DIR__ . '/../modules/poste/app/views/shared/head.php'; ?></head>
<body class="et-portal">
<?php require __DIR__ . '/../modules/poste/app/views/shared/header.php'; ?>
<main id="contenu">
    <section class="et-container et-hero-wrap" aria-labelledby="hero-title">
        <div class="et-hero">
            <img class="et-hero-image" src="<?= etE(etAsset('images/portal/e-tounsi-hero.webp')) ?>" width="1536" height="1024" alt="<?= etE(etT('Une rue tunisienne, ses portes en bois, ses palmiers et son tramway', 'نهج تونسي بأبوابه الخشبيّة ونخيله والترامواي')) ?>" fetchpriority="high">
            <div class="et-hero-copy">
                <span class="et-hero-badge"><span></span><?= etE(etT('Votre Tunisie, à portée de main', 'تونس بين يديك')) ?></span>
                <h1 id="hero-title"><?= etE(etT('Vos services publics,', 'خدماتك العموميّة،')) ?><br><em><?= etE(etT('en toute simplicité.', 'بكلّ سهولة.')) ?></em></h1>
                <p><?= etE(etT('Moins de déplacements, plus de temps pour vous. Retrouvez vos démarches du quotidien dans un seul espace.', 'تنقّلات أقلّ ووقت أكثر ليك. تلقى إجراءاتك اليوميّة في فضاء واحد.')) ?></p>
                <div class="et-hero-actions">
                    <a class="et-button" href="#services"><?= etE(etT('Découvrir les services', 'اكتشف الخدمات')) ?></a>
                    <a class="et-button et-button-white" href="#comment"><?= etE(etT('Comment ça marche', 'كيف يعمل الموقع')) ?></a>
                </div>
            </div>
            <div class="et-hero-caption"><?= etE(etT('Une porte ouverte sur vos démarches', 'باب مفتوح لإجراءاتك')) ?></div>
        </div>
        <form class="et-search" method="get" action="index.php#services" role="search" data-service-search novalidate>
            <input type="hidden" name="lang" value="<?= etE($language) ?>">
            <div class="et-search-field">
                <label class="et-visually-hidden" for="service-query"><?= etE(etT('Rechercher un service', 'ابحث عن خدمة')) ?></label>
                <input id="service-query" name="q" type="search" placeholder="<?= etE(etT('De quel service avez-vous besoin ?', 'شنوّة الخدمة اللي تحتاجها؟')) ?>" value="<?= etE(is_string($_GET['q'] ?? null) ? $_GET['q'] : '') ?>" autocomplete="off">
                <button type="button" class="et-search-clear" data-search-clear aria-label="<?= etE(etT('Effacer la recherche', 'امسح البحث')) ?>" hidden><?= etE(etT('Effacer', 'مسح')) ?></button>
            </div>
            <button class="et-button" type="submit"><?= etE(etT('Rechercher', 'ابحث')) ?></button>
        </form>
        <div class="et-search-hints"><span><?= etE(etT('Par exemple :', 'مثال:')) ?></span><?php foreach ([etT('Finances', 'المالية'), 'CNAM', etT('Transport', 'النقل')] as $etHint): ?><button type="button" data-search-hint="<?= etE($etHint) ?>"><?= etE($etHint) ?></button><?php endforeach; ?></div>
    </section>
    <section class="et-container et-section" id="services" aria-labelledby="services-title">
        <div class="et-section-heading et-heading-row"><div><span class="et-eyebrow"><?= etE(etT('À chaque démarche, sa porte', 'لكلّ إجراء بابه')) ?></span><h2 id="services-title"><?= etE(etT('Entrez, on s’occupe de vous.', 'تفضّل، خدماتنا على ذمّتك.')) ?></h2></div><p><?= etE(etT('Choisissez votre service et poussez la porte de votre administration.', 'اختر خدمتك وافتح باب إدارتك.')) ?></p></div>
        <div class="et-service-ribbon" data-service-ribbon>
            <?php foreach ($etModules as $etKey => $etModule): ?>
                <a class="et-service-photo" href="index.php?page=service&amp;service=<?= etE($etKey) ?>&amp;lang=<?= etE($language) ?>" data-open-office="<?= etE($etKey) ?>" data-name="<?= etE($etModule['nom']) ?>" data-description="<?= etE($etModule['description']) ?>" data-image="<?= etE(etAsset('images/portal/' . $etModule['image'] . '.webp')) ?>" data-search="<?= etE($etModule['nom'] . ' ' . $etModule['mots']) ?>" aria-label="<?= etE(etT('Entrer : ', 'الدخول: ') . $etModule['nom']) ?>">
                    <img src="<?= etE(etAsset('images/portal/' . $etModule['image'] . '.webp')) ?>" alt="" loading="lazy" width="400" height="600">
                    <div class="et-service-photo-shade"></div><span class="et-service-number"><?= etE(str_pad(array_search($etKey, array_keys($etModules)) + 1, 2, '0', STR_PAD_LEFT)) ?></span>
                    <div class="et-service-label">
                        <h3><?= etE($etModule['nom']) ?></h3>
                        <p><?= etE($etModule['court']) ?></p>
                        <span class="et-enter"><?= etE(etT('Entrer', 'الدخول')) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="et-search-status" data-search-status role="status" aria-live="polite" data-none="<?= etE(etT('Aucun service trouvé. Essayez « Poste », « CNAM » ou « Transport ».', 'لا توجد خدمة مطابقة. جرّب « البريد » أو « CNAM » أو « النقل ».')) ?>" data-count="<?= etE(etT('service(s) trouvé(s)', 'خدمة مطابقة')) ?>"></p>
    </section>
    <section class="et-how-section" id="comment" aria-labelledby="how-title">
        <div class="et-container et-section"><div class="et-section-heading et-centered"><span class="et-eyebrow"><?= etE(etT('Simple comme bonjour', 'سهلة كيف السلام')) ?></span><h2 id="how-title"><?= etE(etT('Vos démarches en trois étapes.', 'إجراءاتك في ثلاث مراحل.')) ?></h2></div>
            <div class="et-steps"><?php $etSteps = [[etT('Choisissez votre service', 'اختر خدمتك'), etT('Trouvez l’administration qui correspond à votre besoin.', 'اعثر على الإدارة المناسبة لحاجتك.')], [etT('Entrez dans votre espace', 'ادخل لفضائك'), etT('Ouvrez la porte du bureau et connectez-vous à votre compte.', 'افتح باب المكتب وسجّل الدخول لحسابك.')], [etT('Suivez votre démarche', 'تابع إجراءك'), etT('Retrouvez vos informations et l’avancement de vos demandes.', 'اطّلع على معلوماتك وتقدّم مطالبك.')]]; foreach ($etSteps as $etIndex => $etStep): ?><article class="et-step"><span><?= etE($etIndex + 1) ?></span><h3><?= etE($etStep[0]) ?></h3><p><?= etE($etStep[1]) ?></p></article><?php endforeach; ?></div>
        </div>
    </section>
    <section class="et-container et-section">
        <div class="et-services-callout">
            <div>
                <span class="et-eyebrow"><?= etE(etT('Un accueil pour tous vos services', 'فضاء يجمع خدماتك')) ?></span>
                <h2><?= etE(etT('Une seule page pour vos services publics.', 'صفحة واحدة لخدماتك العموميّة.')) ?></h2>
                <p><?= etE(etT('Transport, municipalité, assurance publique, poste et finances : choisissez votre service pour découvrir son espace.', 'النقل والبلديّة والتأمين العمومي والبريد والمالية: اختر خدمتك واكتشف فضاءها.')) ?></p>
                <a class="et-button" href="#services"><?= etE(etT('Choisir un service', 'اختر خدمة')) ?></a>
            </div>
            <img src="<?= etE(etAsset('images/portal/e-tounsi-hero.webp')) ?>" alt="<?= etE(etT('Les administrations d’une rue tunisienne', 'إدارات في نهج تونسي')) ?>" loading="lazy" width="640" height="440">
        </div>
    </section>
    <section class="et-container et-section et-faq" aria-labelledby="faq-title">
        <div class="et-section-heading">
            <span class="et-eyebrow"><?= etE(etT('On vous répond', 'نجيبوك')) ?></span>
            <h2 id="faq-title"><?= etE(etT('Avant de commencer.', 'قبل ما تبدأ.')) ?></h2>
        </div>
        <div>
            <?php
            $etFaqs = [
                [etT('Quels services sont réunis sur E-Tounsi ?', 'شنوّة الخدمات الموجودة في إي تونسي؟'), etT('L’accueil réunit Transport, Municipalité, Assurance publique — CNAM, Poste et Recette des Finances.', 'الصفحة الرئيسيّة تجمع النقل والبلديّة والتأمين العمومي — CNAM والبريد وقباضة المالية.')],
                [etT('Comment accéder à un service ?', 'كيفاش ندخل لخدمة؟'), etT('Choisissez sa photo dans la bande des cinq services. Sa porte s’ouvre pour vous présenter son espace et les démarches disponibles.', 'اختر صورة الخدمة من بين الخدمات الخمس. يفتح بابها للتعريف بفضائها وبالإجراءات المتاحة.')],
                [etT('Un compte est-il nécessaire ?', 'هل يلزمني حساب؟'), etT('Vous pouvez découvrir les services sans compte. Certaines démarches demandent ensuite de vous connecter dans l’espace concerné.', 'تنجم تكتشف الخدمات من غير حساب. بعض الإجراءات تستحقّ بعد ذلك تسجيل الدخول في الفضاء المعنيّ.')],
            ];
            foreach ($etFaqs as $etFaq): ?>
                <details>
                    <summary><?= etE($etFaq[0]) ?></summary>
                    <p><?= etE($etFaq[1]) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../modules/poste/app/views/shared/footer.php'; ?>
<dialog class="et-office-dialog" id="office-dialog" aria-labelledby="office-title">
    <div class="et-office-toolbar"><button class="et-office-close" type="button" data-office-close><?= etE(etT('Retour aux services', 'العودة إلى الخدمات')) ?></button><span>E-Tounsi <span lang="ar">إي تونسي</span></span></div>
    <?php require __DIR__ . '/../modules/poste/app/views/shared/office.php'; ?>
</dialog>
</body></html>
