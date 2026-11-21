document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.querySelector('.sidebar-wrapper');

    var boutonMobile = document.querySelector('#sidebar-toggle');
    var fond = document.createElement('div');
    fond.className = 'sidebar-overlay';
    document.body.appendChild(fond);

    boutonMobile.addEventListener('click', function () {
        sidebar.classList.toggle('show');
        fond.classList.toggle('show');
    });

    fond.addEventListener('click', function () {
        sidebar.classList.remove('show');
        fond.classList.remove('show');
    });

    var boutonOrdi = document.querySelector('#desktop-sidebar-toggle');
    boutonOrdi.addEventListener('click', function () {
        document.body.classList.toggle('sidebar-minimized');
        var icone = boutonOrdi.querySelector('i');
        if (document.body.classList.contains('sidebar-minimized')) {
            icone.className = 'bi bi-chevron-bar-right';
        } else {
            icone.className = 'bi bi-chevron-bar-left';
        }
    });
});
