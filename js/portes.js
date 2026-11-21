"use strict";

// Les mêmes battants photographiques ouvrent chaque bureau ; l'intérieur vient du service choisi.
(function () {
  const duree = 7200;
  const mouvementReduit = window.matchMedia("(prefers-reduced-motion: reduce)");
  const animations = new WeakMap();
  const panneaux = new Set();

  function limiter(nombre) {
    return Math.min(1, Math.max(0, nombre));
  }

  function assouplir(nombre) {
    const n = limiter(nombre);
    return n * n * n * (n * (n * 6 - 15) + 10);
  }

  function progression(temps, debut, fin) {
    return assouplir((temps - debut) / (fin - debut));
  }

  function obtenirAnimation(panneau) {
    if (animations.has(panneau)) return animations.get(panneau);
    const entree = panneau.querySelector(".et-office-entry--photo");
    if (!entree) return null;
    const etat = {
      panneau,
      entree,
      scene: entree.querySelector(".et-door-stage"),
      camera: entree.querySelector(".et-door-camera"),
      gauche: entree.querySelector(".et-door-left"),
      droite: entree.querySelector(".et-door-right"),
      ombreGauche: entree.querySelector("[data-door-shade-left]"),
      ombreDroite: entree.querySelector("[data-door-shade-right]"),
      ombreInterieur: entree.querySelector(".et-door-interior-shade"),
      interieur: entree.querySelector("[data-door-interior]"),
      plaque: entree.querySelector("[data-door-label]"),
      passer: entree.querySelector("[data-door-skip]"),
      accueil: panneau.querySelector(".et-office-welcome"),
      statut: panneau.querySelector("[data-door-status]"),
      frame: 0,
      version: 0,
      temps: 0,
      precedent: 0,
      actif: false,
    };
    function dimensionner() {
      etat.scene.style.setProperty("--et-door-scale", String(etat.scene.clientWidth / 877));
    }
    etat.observer = new ResizeObserver(dimensionner);
    etat.observer.observe(etat.scene);
    dimensionner();
    etat.passer.addEventListener("click", function () { terminer(etat); });
    animations.set(panneau, etat);
    panneaux.add(panneau);
    return etat;
  }

  function dessiner(etat, temps) {
    etat.temps = Math.min(duree, Math.max(0, temps));
    const gauche = progression(etat.temps, 480, 4700);
    const droite = progression(etat.temps, 640, 4890);
    const avance = progression(etat.temps, 2400, 6400);
    const ouverture = (gauche + droite) / 2;
    etat.gauche.style.transform = "rotateY(" + gauche * 83 + "deg)";
    etat.droite.style.transform = "rotateY(" + -droite * 83 + "deg)";
    etat.ombreGauche.style.opacity = String(gauche * .29);
    etat.ombreDroite.style.opacity = String(droite * .22);
    etat.ombreInterieur.style.opacity = String(.42 * (1 - ouverture) + .03);
    etat.camera.style.transform = "scale(" + (1 + avance * .115) + ") translateY(" + -avance * 12 + "px)";
    // Le dernier fondu relie la porte au grand intérieur déjà présent dans la page.
    etat.entree.style.opacity = String(1 - progression(etat.temps, 6250, duree));
  }

  function arreter(etat) {
    cancelAnimationFrame(etat.frame);
    etat.frame = 0;
    etat.precedent = 0;
    etat.actif = false;
  }

  function terminer(etat) {
    const rendreFocus = document.activeElement === etat.passer;
    arreter(etat);
    etat.version++;
    dessiner(etat, duree);
    etat.entree.style.visibility = "hidden";
    etat.entree.style.pointerEvents = "none";
    etat.accueil.removeAttribute("inert");
    etat.panneau.classList.remove("et-door-opening");
    etat.panneau.classList.add("is-open");
    etat.panneau.dataset.doorState = "open";
    etat.statut.textContent = etat.statut.dataset.open;
    if (rendreFocus) etat.panneau.querySelector("[data-office-scroll]").focus({preventScroll:true});
  }

  function avancer(etat, maintenant) {
    if (!etat.actif) return;
    const ecoule = etat.precedent ? Math.min(80, maintenant - etat.precedent) : 0;
    etat.precedent = maintenant;
    dessiner(etat, etat.temps + ecoule);
    if (etat.temps >= duree) {
      terminer(etat);
    } else {
      etat.frame = requestAnimationFrame(function (temps) { avancer(etat, temps); });
    }
  }

  window.etFermerPortes = function (panneau) {
    const etat = obtenirAnimation(panneau);
    if (!etat) return;
    arreter(etat);
    etat.version++;
    dessiner(etat, 0);
    etat.entree.style.visibility = "visible";
    etat.entree.style.pointerEvents = "auto";
    etat.accueil.removeAttribute("inert");
    panneau.classList.remove("is-open", "et-door-opening");
    panneau.dataset.doorState = "closed";
    etat.statut.textContent = "";
  };

  window.etOuvrirPortes = function (panneau) {
    const etat = obtenirAnimation(panneau);
    if (!etat) { panneau.classList.add("is-open"); return; }
    window.etFermerPortes(panneau);
    const version = etat.version;
    const imageBureau = panneau.querySelector("[data-office-photo]");
    etat.interieur.src = imageBureau.src;
    etat.plaque.classList.toggle("et-door-plaque-long", etat.plaque.textContent.trim().length > 15);
    panneau.classList.add("et-door-opening");
    panneau.dataset.doorState = "opening";
    etat.accueil.setAttribute("inert", "");
    etat.statut.textContent = etat.statut.dataset.opening;
    if (mouvementReduit.matches) { terminer(etat); return; }
    const images = Array.from(etat.entree.querySelectorAll("img"));
    Promise.all(images.map(function (image) { return image.decode(); })).then(function () {
      if (version !== etat.version) return;
      etat.actif = true;
      etat.frame = requestAnimationFrame(function (temps) { avancer(etat, temps); });
    }).catch(function () {
      // Un fichier manquant ne doit jamais empêcher l'accès au bureau réel.
      if (version === etat.version) terminer(etat);
    });
  };

  mouvementReduit.addEventListener("change", function (event) {
    if (!event.matches) return;
    panneaux.forEach(function (panneau) {
      const etat = animations.get(panneau);
      if (panneau.dataset.doorState === "opening") terminer(etat);
    });
  });
  document.addEventListener("visibilitychange", function () {
    panneaux.forEach(function (panneau) {
      const etat = animations.get(panneau);
      if (etat.actif) etat.precedent = 0;
    });
  });
})();
