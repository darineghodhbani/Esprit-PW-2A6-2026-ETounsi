"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const mobileToggle = document.getElementById("sidebar-toggle");
    const desktopToggle = document.getElementById("desktop-sidebar-toggle");
    const overlay = document.querySelector(".sidebar-overlay");
    const compact = window.matchMedia("(max-width: 1200px)");

    function syncMobileNavigation() {
        const open = compact.matches && sidebar.classList.contains("show");
        sidebar.inert = compact.matches && !open;
        mobileToggle.setAttribute("aria-expanded", String(open));
        mobileToggle.setAttribute("aria-label", open ? "Fermer la navigation" : "Ouvrir la navigation");
    }

    function closeMobileNavigation() {
        sidebar.classList.remove("show");
        overlay?.classList.remove("show");
        syncMobileNavigation();
    }

    mobileToggle.addEventListener("click", () => {
        syncMobileNavigation();
        if (sidebar.classList.contains("show")) {
            sidebar.querySelector("a")?.focus();
        }
    });

    desktopToggle.addEventListener("click", () => {
        const minimized = document.body.classList.contains("sidebar-minimized");
        desktopToggle.setAttribute("aria-label", minimized ? "Développer la navigation" : "Réduire la navigation");
    });

    overlay?.addEventListener("click", () => {
        syncMobileNavigation();
        mobileToggle.focus();
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && compact.matches && sidebar.classList.contains("show")) {
            closeMobileNavigation();
            mobileToggle.focus();
        }
    });

    compact.addEventListener("change", closeMobileNavigation);
    syncMobileNavigation();

    document.querySelector("[aria-invalid='true']")?.focus();

    for (const form of document.querySelectorAll("form[data-poste-save]")) {
        const button = form.querySelector("button[type=submit]");
        form.addEventListener("submit", () => {
            button.disabled = true;
            button.setAttribute("aria-busy", "true");
        });
        window.addEventListener("pageshow", () => {
            button.disabled = false;
            button.removeAttribute("aria-busy");
        });
    }
});

document.querySelectorAll("[data-password-toggle]").forEach(function (button) {
    button.addEventListener("click", function () {
        const input = document.getElementById(button.dataset.passwordToggle);
        const visible = input.type === "password";
        input.type = visible ? "text" : "password";
        button.setAttribute("aria-pressed", String(visible));
        button.setAttribute("aria-label", visible ? "Masquer le mot de passe" : "Afficher le mot de passe");
        button.querySelector("i").className = visible ? "bi bi-eye-slash" : "bi bi-eye";
    });
});