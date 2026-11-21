"use strict";

// Ouverture des deux portes rouges du premier prototype, adaptée au DOM PHP.
(function () {
  const mouvementReduit = window.matchMedia("(prefers-reduced-motion: reduce)");
  const delais = new Map();

  function annuler(panneau) {
    const timers = delais.get(panneau) || [];
    timers.forEach(clearTimeout);
    delais.delete(panneau);
  }

  function terminer(panneau) {
    annuler(panneau);
    panneau.classList.remove("et-door-opening");
    panneau.classList.add("et-door-reveal", "is-open");
    panneau.dataset.doorState = "open";
    panneau.querySelector(".et-office-welcome").removeAttribute("inert");
    const statut = panneau.querySelector("[data-door-status]");
    statut.textContent = statut.dataset.open;
  }

  window.etFermerPortes = function (panneau) {
    annuler(panneau);
    panneau.classList.remove("et-door-opening", "et-door-reveal", "is-open");
    panneau.dataset.doorState = "closed";
    panneau.querySelector(".et-office-welcome").removeAttribute("inert");
    panneau.querySelector("[data-door-status]").textContent = "";
  };

  window.etOuvrirPortes = function (panneau) {
    window.etFermerPortes(panneau);
    if (mouvementReduit.matches) {
      terminer(panneau);
      return;
    }
    panneau.classList.add("et-door-opening");
    panneau.dataset.doorState = "opening";
    panneau.querySelector(".et-office-welcome").setAttribute("inert", "");
    const statut = panneau.querySelector("[data-door-status]");
    statut.textContent = statut.dataset.opening;
    const debut = setTimeout(function () {
      panneau.classList.add("et-door-reveal");
    }, 150);
    const fin = setTimeout(function () { terminer(panneau); }, 1550);
    delais.set(panneau, [debut, fin]);
  };

  mouvementReduit.addEventListener("change", function (event) {
    if (event.matches) Array.from(delais.keys()).forEach(terminer);
  });
})();
