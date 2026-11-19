'use strict';

const menu = document.getElementById('sidebar');
const boutonMenu = document.getElementById('sidebar-toggle');
const boutonReduire = document.getElementById('desktop-sidebar-toggle');
const fondMenu = document.createElement('div');
fondMenu.className = 'sidebar-overlay';
document.body.appendChild(fondMenu);

function actualiserMenu() {
    const mobile = window.innerWidth <= 1200;
    const ouvert = mobile && menu.classList.contains('show');

    boutonMenu.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
    boutonMenu.setAttribute('aria-label', ouvert ? 'Fermer la navigation' : 'Ouvrir la navigation');
    fondMenu.classList.toggle('show', ouvert);
    if (mobile && !ouvert) {
        menu.setAttribute('inert', '');
    } else {
        menu.removeAttribute('inert');
    }
}

function fermerMenu() {
    menu.classList.remove('show');
    actualiserMenu();
}

boutonMenu.addEventListener('click', function () {
    menu.classList.toggle('show');
    actualiserMenu();
    if (menu.classList.contains('show')) {
        menu.querySelector('a').focus();
    }
});

fondMenu.addEventListener('click', function () {
    fermerMenu();
    boutonMenu.focus();
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && menu.classList.contains('show')) {
        fermerMenu();
        boutonMenu.focus();
    }
});

window.addEventListener('resize', fermerMenu);
actualiserMenu();

boutonReduire.addEventListener('click', function () {
    const reduit = document.body.classList.toggle('sidebar-minimized');
    boutonReduire.setAttribute('aria-label', reduit ? 'Développer la navigation' : 'Réduire la navigation');
});

document.querySelectorAll('[data-password-toggle]').forEach(function (bouton) {
    bouton.addEventListener('click', function () {
        const champ = document.getElementById(bouton.dataset.passwordToggle);
        const visible = champ.type === 'password';
        champ.type = visible ? 'text' : 'password';
        bouton.setAttribute('aria-pressed', visible ? 'true' : 'false');
        bouton.setAttribute('aria-label', visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        bouton.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
});

const premierChampInvalide = document.querySelector('[aria-invalid="true"]');
if (premierChampInvalide) {
    premierChampInvalide.focus();
}

document.querySelectorAll('form[data-poste-save]').forEach(function (formulaire) {
    const bouton = formulaire.querySelector('button[type="submit"]');
    formulaire.addEventListener('submit', function () {
        bouton.disabled = true;
        bouton.setAttribute('aria-busy', 'true');
    });
    window.addEventListener('pageshow', function () {
        bouton.disabled = false;
        bouton.removeAttribute('aria-busy');
    });
});
