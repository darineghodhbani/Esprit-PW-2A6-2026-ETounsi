window.addEventListener('load', () => {
    const spinner = document.getElementById('spinner');
    if (spinner) {
        spinner.classList.add('hide');
    }
});

const menuToggle = document.querySelector('[data-menu-toggle]');
const mainNav = document.getElementById('main-nav');
if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', () => {
        const collapsed = mainNav.dataset.collapsed === 'true';
        mainNav.dataset.collapsed = String(!collapsed);
        menuToggle.setAttribute('aria-expanded', String(collapsed));
    });
}

const backToTop = document.getElementById('backToTop');
if (backToTop) {
    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
