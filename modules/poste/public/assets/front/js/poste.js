const boutonRetour = document.querySelector('.back-to-top');

function afficherBoutonRetour() {
    boutonRetour.hidden = window.scrollY < 240;
}

window.addEventListener('scroll', afficherBoutonRetour);
afficherBoutonRetour();

const boutonsMotDePasse = document.querySelectorAll('[data-password]');
boutonsMotDePasse.forEach(function (bouton) {
    bouton.addEventListener('click', function () {
        const champ = document.getElementById(bouton.dataset.password);
        const afficher = champ.type === 'password';
        champ.type = afficher ? 'text' : 'password';
        bouton.textContent = afficher ? bouton.dataset.hide : bouton.dataset.show;
        bouton.setAttribute('aria-pressed', afficher ? 'true' : 'false');
        const label = document.querySelector('label[for="' + champ.id + '"]').textContent;
        bouton.setAttribute('aria-label', bouton.textContent + ' ' + label);
    });
});

const premierChampInvalide = document.querySelector('[aria-invalid="true"]');
if (premierChampInvalide) {
    premierChampInvalide.focus();
}
