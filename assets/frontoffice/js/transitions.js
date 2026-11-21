(() => {
    'use strict';

    const veil = document.createElement('div');
    veil.id = 'page-transition';
    veil.setAttribute('aria-hidden', 'true');
    document.body.appendChild(veil);

    const DURATION = 380;
    let leaving = false;

    const go = (href) => {
        if (leaving) return;
        leaving = true;
        veil.classList.add('active');
        window.setTimeout(() => { location.assign(href); }, DURATION);
    };
    window.ETNav = { go };

    const isInternal = (link) => {
        if (!link.href) return false;
        const url = new URL(link.href, location.href);
        return url.origin === location.origin && /index\.php/.test(url.pathname);
    };

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 ||
            event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const link = event.target instanceof Element
            ? event.target.closest('a[href]') : null;
        if (!link || link.target === '_blank' || link.hasAttribute('download')) return;
        if (!isInternal(link)) return;
        const url = new URL(link.href, location.href);
        if (url.search === location.search && url.hash) return;
        if (link.hasAttribute('data-hotspot')) return;
        event.preventDefault();
        go(url.href);
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.action.includes('index.php')) return;
        if (form.dataset.pending === 'true') return;
        form.dataset.pending = 'true';
        veil.classList.add('active');
    });

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            veil.classList.remove('active');
            leaving = false;
        }
    });
})();
