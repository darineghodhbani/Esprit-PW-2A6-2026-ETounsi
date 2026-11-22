<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#FAF7F2">
<meta name="description" content="E-Tounsi — Toute votre journée. Un seul parcours.">
<link rel="icon" type="image/svg+xml" href="assets/frontoffice/img/brand/e-tounsi-mark.svg">
<title><?= htmlspecialchars($title ?? 'E-Tounsi') ?></title>
<?php if (($currentPage ?? '') === 'home'): ?>
<link rel="preload" as="image" href="assets/frontoffice/img/street/street-desktop.webp"
      imagesrcset="assets/frontoffice/img/street/street-mobile.webp 900w, assets/frontoffice/img/street/street-desktop.webp 1678w"
      imagesizes="(max-width: 700px) 100vw, 96vw">
<?php endif; ?>
<link rel="stylesheet" href="assets/frontoffice/css/tokens.css">
<link rel="stylesheet" href="assets/frontoffice/css/base.css">
<link rel="stylesheet" href="assets/frontoffice/css/components.css">
<link rel="stylesheet" href="assets/frontoffice/css/layouts.css">
<link rel="stylesheet" href="assets/frontoffice/css/etounsi.css">
<link rel="stylesheet" href="assets/frontoffice/css/pages/home.css">
<link rel="stylesheet" href="assets/frontoffice/css/pages/auth.css">
<link rel="stylesheet" href="assets/frontoffice/css/pages/module.css">
<link rel="stylesheet" href="assets/frontoffice/css/pages/workspace.css">
<link rel="stylesheet" href="assets/frontoffice/css/pages/info.css">

<link rel="stylesheet" href="assets/frontoffice/css/pages/poste.css">
