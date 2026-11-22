document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            var visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', visible ? 'true' : 'false');
            button.setAttribute('aria-label', visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });
    });
    document.querySelectorAll('form[data-poste-save]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (event.defaultPrevented) return;
            if (form.dataset.envoi === 'oui') {
                event.preventDefault();
                return;
            }
            form.dataset.envoi = 'oui';
            form.querySelectorAll('button[type="submit"]').forEach(function (button) {
                button.disabled = true;
            });
        });
    });
});
