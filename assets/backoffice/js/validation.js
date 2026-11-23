(function () {
    'use strict';

    var rules = {
        required: function (v) {
            return v.trim() !== '' ? null : 'Ce champ est obligatoire.';
        },
        name: function (v) {
            return /^[A-Za-zÀ-ÖØ-öø-ÿ][A-Za-zÀ-ÖØ-öø-ÿ '\-]{1,49}$/.test(v.trim()) ? null : 'Utilisez 2 à 50 lettres.';
        },
        email: function (v) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? null : "L'email n'est pas valide.";
        },
        phone: function (v) {
            return /^[2-9][0-9]{7}$/.test(v.trim()) ? null : 'Le téléphone doit contenir 8 chiffres (ex : 22123456).';
        },
        cin: function (v) {
            return /^[0-9]{8}$/.test(v.trim()) ? null : 'Le numéro CIN doit contenir exactement 8 chiffres.';
        },
        password: function (v) {
            return (v.length >= 8 && /[A-Z]/.test(v) && /[a-z]/.test(v) && /[0-9]/.test(v))
                ? null : 'Au moins 8 caractères, dont une majuscule, une minuscule et un chiffre.';
        },
        match: function (v, otherId) {
            var other = document.getElementById(otherId);
            return other && other.value === v ? null : 'Les deux valeurs ne sont pas identiques.';
        },
        min: function (v, n) {
            return v.trim().length >= parseInt(n, 10) ? null : 'Au moins ' + n + ' caractères.';
        },
        max: function (v, n) {
            return v.trim().length <= parseInt(n, 10) ? null : 'Au maximum ' + n + ' caractères.';
        },
        number: function (v) {
            return /^[0-9]+$/.test(v.trim()) ? null : 'Entrez un nombre entier positif.';
        },
        date: function (v) {
            return !isNaN(Date.parse(v)) ? null : "La date n'est pas valide.";
        },
        futureDate: function (v) {
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var d = new Date(v);
            return !isNaN(d) && d >= today ? null : "La date doit être aujourd'hui ou plus tard.";
        }
    };

        function checkField(field) {
        var list = (field.getAttribute('data-validate') || '').split('|');
        var value = field.value || '';
        var isRequired = list.indexOf('required') !== -1;

        for (var i = 0; i < list.length; i++) {
            if (!list[i]) continue;
            var parts = list[i].split(':');
            var rule = rules[parts[0]];
            if (!rule) continue;

            if (!isRequired && parts[0] !== 'required' && value.trim() === '') continue;
            var error = rule(value, parts[1]);
            if (error) return error;
        }
        return null;
    }

    function showError(field, message) {
        var anchor = field.closest('.form-floating, .input-group') || field;
        var container = anchor.parentNode;

        container.querySelectorAll('.js-error, .server-error').forEach(function (el) { el.remove(); });

        if (message) {
            field.classList.add('is-invalid', 'is-invalid-custom');
            field.classList.remove('is-valid-custom');
            var div = document.createElement('div');
            div.className = 'invalid-feedback d-block form-feedback-custom invalid-custom js-error';
            div.textContent = message;
            anchor.insertAdjacentElement('afterend', div);
        } else {
            field.classList.remove('is-invalid', 'is-invalid-custom');
        }
    }

    function validateForm(form) {
        var ok = true;
        var first = null;
        form.querySelectorAll('[data-validate]').forEach(function (field) {
            var error = checkField(field);
            showError(field, error);
            if (error) {
                ok = false;
                if (!first) first = field;
            }
        });
        if (first) first.focus();
        return ok;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var customValid = validateForm(form);
                var nativeValid = form.checkValidity();

                if (!customValid || !nativeValid) {
                    event.preventDefault();
                    if (!nativeValid) form.reportValidity();
                }
            });
            form.querySelectorAll('[data-validate]').forEach(function (field) {
                field.addEventListener('blur', function () { showError(field, checkField(field)); });
                field.addEventListener('input', function () {
                    if (field.classList.contains('is-invalid')) showError(field, checkField(field));
                });
            });
        });

        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!event.defaultPrevented && !window.confirm(form.getAttribute('data-confirm'))) {
                    event.preventDefault();
                }
            });
        });
    });
})();
