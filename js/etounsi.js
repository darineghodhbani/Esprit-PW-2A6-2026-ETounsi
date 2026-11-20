"use strict";

const etArabic = document.documentElement.lang === "ar";
const etMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
const etHeader = document.querySelector(".et-header");
const etMenu = document.querySelector("[data-menu-toggle]");

function etCloseMenu() {
  if (!etMenu) return;
  etHeader.classList.remove("menu-open");
  etMenu.setAttribute("aria-expanded", "false");
}
if (etMenu) {
  etMenu.addEventListener("click", function () {
    const open = etHeader.classList.toggle("menu-open");
    etMenu.setAttribute("aria-expanded", String(open));
  });
  etHeader.querySelectorAll("nav a").forEach(function (link) {
    link.addEventListener("click", etCloseMenu);
  });
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && etHeader.classList.contains("menu-open")) {
      etCloseMenu();
      etMenu.focus();
    }
  });
  window.addEventListener("resize", etCloseMenu);
}
document.querySelectorAll(".et-account-menu").forEach(function (menu) {
  document.addEventListener("click", function (event) {
    if (!menu.contains(event.target)) menu.open = false;
  });
  menu.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      menu.open = false;
      menu.querySelector("summary").focus();
    }
  });
});

// Les cinq services sont déjà dans la page : la recherche n'envoie aucune donnée.
const etSearch = document.querySelector("[data-service-search]");
const etCards = Array.from(document.querySelectorAll("[data-open-office]"));
function etNormalize(value) {
  return value
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLocaleLowerCase()
    .trim();
}
if (etSearch) {
  const field = etSearch.querySelector("[name=q]");
  const clear = etSearch.querySelector("[data-search-clear]");
  const status = document.querySelector("[data-search-status]");
  function filterServices(updateUrl) {
    const words = etNormalize(field.value).split(/\s+/).filter(Boolean);
    let count = 0;
    etCards.forEach(function (card) {
      const text = etNormalize(card.dataset.search);
      const match = words.every(function (word) {
        return text.includes(word);
      });
      card.hidden = !match;
      if (match) count++;
    });
    clear.hidden = field.value.length === 0;
    status.textContent = words.length
      ? count
        ? count + " " + status.dataset.count
        : status.dataset.none
      : "";
    if (updateUrl) {
      const url = new URL(location.href);
      if (field.value) url.searchParams.set("q", field.value);
      else url.searchParams.delete("q");
      history.replaceState(null, "", url);
    }
  }
  field.addEventListener("input", function (event) {
    if (!event.isComposing) filterServices(true);
  });
  field.addEventListener("compositionend", function () {
    filterServices(true);
  });
  clear.addEventListener("click", function () {
    field.value = "";
    filterServices(true);
    field.focus();
  });
  etSearch.addEventListener("submit", function (event) {
    event.preventDefault();
    filterServices(true);
    document
      .getElementById("services")
      .scrollIntoView({ behavior: etMotion.matches ? "instant" : "smooth" });
  });
  document.querySelectorAll("[data-search-hint]").forEach(function (button) {
    button.addEventListener("click", function () {
      field.value = button.dataset.searchHint;
      filterServices(true);
      field.focus();
    });
  });
  window.addEventListener("popstate", function () {
    field.value = new URL(location.href).searchParams.get("q") || "";
    filterServices(false);
  });
  filterServices(false);
}

const etDialog = document.getElementById("office-dialog");
let etOpener = null;
let etDoorTimer;
function etOpenDoors(panel) {
  clearTimeout(etDoorTimer);
  panel.classList.remove("is-open");
  // Deux images successives laissent d'abord voir la porte fermée.
  etDoorTimer = window.setTimeout(
    function () {
      panel.classList.add("is-open");
    },
    etMotion.matches ? 0 : 180,
  );
}
function etServiceContent(panel, key) {
  const poste = key === "poste";
  panel.dataset.service = key;
  panel.querySelector("[data-office-poste]").hidden = !poste;
  panel.querySelector("[data-office-other]").hidden = poste;
  panel.querySelector("[data-office-cta]").textContent = poste
    ? etArabic
      ? "مكتب البريد"
      : "Bureau de poste"
    : etArabic
      ? "اكتشف الخدمة"
      : "Découvrir ce service";
}
if (etDialog && typeof etDialog.showModal === "function") {
  const panel = etDialog.querySelector("[data-office-panel]");
  etCards.forEach(function (card) {
    card.addEventListener("click", function (event) {
      if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey)
        return;
      event.preventDefault();
      etOpener = card;
      panel.querySelector("[data-office-photo]").src = card.dataset.image;
      panel.querySelector("[data-office-title]").textContent =
        card.dataset.name;
      panel.querySelector("[data-other-title]").textContent = card.dataset.name;
      panel.querySelector("[data-door-label]").textContent = card.dataset.name;
      panel.querySelector("[data-office-description]").textContent =
        card.dataset.description;
      etServiceContent(panel, card.dataset.openOffice);
      etDialog.showModal();
      document.body.classList.add("et-modal-open");
      etDialog.scrollTop = 0;
      etDialog
        .querySelector("[data-office-close]")
        .focus({ preventScroll: true });
      etOpenDoors(panel);
    });
  });
  etDialog
    .querySelector("[data-office-close]")
    .addEventListener("click", function () {
      etDialog.close();
    });
  etDialog.addEventListener("close", function () {
    clearTimeout(etDoorTimer);
    document.body.classList.remove("et-modal-open");
    panel.classList.remove("is-open");
    panel.querySelectorAll("input[name=password]").forEach(function (input) {
      input.value = "";
      input.type = "password";
    });
    panel.querySelectorAll("[data-password]").forEach(function (button) {
      button.textContent = button.dataset.show;
      button.setAttribute("aria-pressed", "false");
    });
    if (etOpener && etOpener.isConnected)
      etOpener.focus({ preventScroll: true });
  });
}
document.querySelectorAll("[data-office-panel]").forEach(function (panel) {
  if (!panel.closest("dialog")) {
    etServiceContent(panel, panel.dataset.service);
    etOpenDoors(panel);
  }
  panel
    .querySelector("[data-office-scroll]")
    .addEventListener("click", function (event) {
      event.preventDefault();
      const access = panel.querySelector(".et-office-access");
      access.scrollIntoView({
        behavior: etMotion.matches ? "instant" : "smooth",
        block: "start",
      });
      access.focus({ preventScroll: true });
    });
});

// Comportements communs des formulaires PHP, sans modifier leurs actions.
document.querySelectorAll("[data-password]").forEach(function (button) {
  button.addEventListener("click", function () {
    const input = document.getElementById(button.dataset.password);
    const show = input.type === "password";
    input.type = show ? "text" : "password";
    button.textContent = show ? button.dataset.hide : button.dataset.show;
    button.setAttribute("aria-pressed", String(show));
    button.setAttribute(
      "aria-label",
      button.textContent + " " + (etArabic ? "كلمة المرور" : "le mot de passe"),
    );
  });
});
const etFirstInvalid = document.querySelector("[aria-invalid=true]");
if (etFirstInvalid) etFirstInvalid.focus();
document.querySelectorAll("form[method=post]").forEach(function (form) {
  form.addEventListener("submit", function (event) {
    if (event.defaultPrevented) return;
    if (form.dataset.sending === "true") {
      event.preventDefault();
      return;
    }
    form.dataset.sending = "true";
    const button = event.submitter || form.querySelector("button[type=submit]");
    if (button) {
      button.style.minWidth = button.offsetWidth + "px";
      button.disabled = true;
      button.setAttribute("aria-busy", "true");
    }
  });
});
window.addEventListener("pageshow", function () {
  document.querySelectorAll("form[data-sending]").forEach(function (form) {
    delete form.dataset.sending;
    form.querySelectorAll("[aria-busy=true]").forEach(function (button) {
      button.disabled = false;
      button.removeAttribute("aria-busy");
    });
  });
});
document
  .querySelectorAll("form:not([data-service-search]) input[type=search]")
  .forEach(function (input) {
    if (!input.name) return;
    const clear = document.createElement("button");
    clear.type = "button";
    clear.className = "et-clear-inline";
    clear.textContent = etArabic ? "مسح البحث" : "Effacer la recherche";
    clear.hidden = !input.value;
    input.insertAdjacentElement("afterend", clear);
    input.addEventListener("input", function () {
      clear.hidden = !input.value;
    });
    clear.addEventListener("click", function () {
      input.value = "";
      clear.hidden = true;
      input.focus();
      input.dispatchEvent(new Event("input", { bubbles: true }));
    });
  });
const etTop = document.querySelector(".back-to-top");
if (etTop) {
  function updateTop() {
    etTop.hidden = scrollY < 300;
  }
  window.addEventListener("scroll", updateTop, { passive: true });
  updateTop();
}
