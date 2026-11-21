(() => {
    'use strict';

    const root = document.querySelector('[data-portal]');
    if (!root || typeof gsap === 'undefined') return;

    gsap.registerPlugin(ScrollToPlugin, ScrollTrigger);

    const readJson = (selector) => {
        const el = root.querySelector(selector);
        try {
            return JSON.parse(el?.textContent ?? 'null');
        } catch {
            return null;
        }
    };
    const modules = (readJson('[data-administrations]') ?? []).filter((m) =>
        m && typeof m.id === 'string' &&
        typeof m.publicPath === 'string' &&
        typeof m.interior === 'string' &&
        typeof m.label === 'string' &&
        typeof m.welcome === 'string' &&
        typeof m.cta === 'string' &&
        Number.isFinite(m.approachScale) &&
        m.hotspot && Number.isFinite(m.hotspot.u) && Number.isFinite(m.hotspot.v) &&
        m.threshold && Number.isFinite(m.threshold.u) && Number.isFinite(m.threshold.v)
    );
    if (!modules.length) return;
    const i18n = readJson('[data-scene-i18n]') ?? {};

    const must = (scope, selector) => {
        const el = scope.querySelector(selector);
        if (!el) throw new Error('Élément du portail manquant : ' + selector);
        return el;
    };
    const stage       = must(root, '[data-scene-stage]');
    const plane       = must(root, '[data-street-plane]');
    const viewport    = must(root, '[data-camera-viewport]');
    const interiorImg = must(root, '[data-interior]');
    const welcome     = must(root, '[data-welcome]');
    const welcomeTitle = must(root, '[data-welcome-title]');
    const handoff     = must(root, '[data-handoff]');
    const handoffLabel = root.querySelector('[data-handoff-label]');
    const controls    = must(root, '[data-portal-controls]');
    const direct      = must(root, '[data-direct]');
    const status      = must(root, '[data-scene-status]');
    const intro       = root.querySelector('[data-scene-intro]');

    const machine = {
        state: 'street',
        transitions: {
            street:      ['loading', 'returning'],
            loading:     ['approaching', 'returning', 'error'],
            approaching: ['threshold', 'returning', 'error'],
            threshold:   ['interior', 'returning', 'error'],
            interior:    ['handoff', 'returning', 'error'],
            handoff:     ['returning', 'error'],
            returning:   ['street'],
            error:       ['returning', 'loading'],
        },
        move(to) {
            if (to === this.state) return;
            if (!this.transitions[this.state].includes(to)) {
                throw new Error(`Transition invalide : ${this.state} → ${to}`);
            }
            this.state = to;
        },
    };
    const move = (state) => {
        machine.move(state);
        root.dataset.sceneState = state;
        stage.setAttribute('aria-busy',
            String(['loading', 'approaching', 'threshold', 'handoff'].includes(state)));
    };

    const quality = {
        reduced: false,
        compact: false,
        get direct() { return this.reduced || this.compact; },
    };

    let runId = 0;
    let execution;
    let current;
    let previousFocus;
    let tween;

    const SOURCE = { width: 1678, height: 937 };
    const coverPoint = (point, source, rect) => {
        const scale = Math.max(rect.width / source.width, rect.height / source.height);
        return {
            x: (rect.width - source.width * scale) / 2 + point.u * source.width * scale,
            y: (rect.height - source.height * scale) / 2 + point.v * source.height * scale,
        };
    };

    const frameViewport = () => {
        const offset = Math.max(0, -stage.getBoundingClientRect().top);
        root.style.setProperty('--scene-camera-top', `${offset}px`);
    };

    const repositionHotspots = () => {
        const rect = plane.getBoundingClientRect();
        const stageRect = stage.getBoundingClientRect();
        for (const link of plane.querySelectorAll('[data-hotspot]')) {
            const module = modules.find((m) => m.id === link.dataset.hotspot);
            if (!module) continue;
            const point = coverPoint(module.hotspot, SOURCE, rect);
            link.style.left = `${point.x}px`;
            link.style.top = `${point.y}px`;
            const x = rect.left - stageRect.left + point.x;
            const y = rect.top - stageRect.top + point.y;
            link.hidden = x < 22 || x > stageRect.width - 22 ||
                          y < 22 || y > stageRect.height - 22;
        }
    };

    const animate = (build, signal) => new Promise((resolve, reject) => {
        const abort = () => {
            tween?.kill();
            reject(new DOMException('Annulé', 'AbortError'));
        };
        const tl = gsap.timeline({
            paused: true,
            onComplete: () => {
                signal.removeEventListener('abort', abort);
                resolve();
            },
        });
        tween = tl;
        signal.addEventListener('abort', abort, { once: true });
        if (signal.aborted) { abort(); return; }
        build(tl);
        tl.play();
    });

    const loadInterior = (src, signal, timeoutMs = 8000) => new Promise((resolve, reject) => {
        const image = new Image();
        const cleanup = () => {
            window.clearTimeout(timeout);
            image.onload = null;
            image.onerror = null;
            signal.removeEventListener('abort', abort);
        };
        const fail = (error) => { cleanup(); image.removeAttribute('src'); reject(error); };
        const abort = () => fail(new DOMException('Annulé', 'AbortError'));
        const timeout = window.setTimeout(() => fail(new Error('Chargement trop long')), timeoutMs);
        image.onload = () => { cleanup(); resolve(image); };
        image.onerror = () => fail(new Error('Intérieur indisponible'));
        signal.addEventListener('abort', abort, { once: true });
        if (signal.aborted) { abort(); return; }
        image.src = src;
    });

    const clearScene = () => {
        tween?.kill();
        gsap.killTweensOf(welcome);
        interiorImg.hidden = true;
        interiorImg.removeAttribute('src');
        gsap.set(interiorImg, { clearProps: 'all' });
        welcome.hidden = true;
        gsap.set(plane, { clearProps: 'all' });
        gsap.set(welcome, { clearProps: 'all' });
        root.style.removeProperty('--scene-camera-top');
    };
    const stopExecution = () => {
        runId += 1;
        execution?.abort();
        execution = undefined;
    };
    const reset = (focus = false, animateOut = false) => {
        stopExecution();
        move('returning');
        if (animateOut && !quality.reduced) {
            gsap.timeline({
                onComplete: () => { clearScene(); repositionHotspots(); },
            })
                .to(welcome, { opacity: 0, y: 10, duration: 0.35, ease: 'sine.in' }, 0)
                .to(interiorImg, { opacity: 0, duration: 0.55, ease: 'sine.inOut' }, 0)
                .to(plane, { scale: 1, x: 0, y: 0, duration: 0.8, ease: 'sine.inOut' }, 0);
        } else {
            clearScene();
            repositionHotspots();
        }
        plane.inert = false;
        controls.hidden = true;
        status.textContent = '';
        move('street');
        if (focus) previousFocus?.focus({ preventScroll: true });
    };
    const fail = () => {
        clearScene();
        frameViewport();
        plane.inert = false;
        move('error');
        controls.hidden = false;
        status.textContent = i18n.error ?? 'La visite n’a pas pu se charger.';
    };

    const select = async (module, element) => {
        reset();
        frameViewport();
        current = module;
        previousFocus = element;
        const id = runId;
        const controller = new AbortController();
        execution = controller;
        direct.href = module.publicPath;
        handoff.href = module.publicPath;
        if (handoffLabel) handoffLabel.textContent = module.cta;
        else handoff.textContent = module.cta;
        welcomeTitle.textContent = module.welcome;
        controls.hidden = false;
        plane.inert = true;
        move('loading');
        status.textContent = (i18n.loading ?? 'Préparation de la visite · {label}…')
            .replace('{label}', module.label);
        try {
            const image = await loadInterior(module.interior, controller.signal);
            if (id !== runId) return;
            move('approaching');
            const rect = plane.getBoundingClientRect();
            const point = coverPoint(module.threshold, SOURCE, rect);
            const stageRect = viewport.getBoundingClientRect();
            await animate((tl) => {
                tl.to(plane, {
                    scale: module.approachScale,
                    x: stageRect.width / 2 - (rect.left - stageRect.left + point.x),
                    y: stageRect.height / 2 - (rect.top - stageRect.top + point.y),
                    transformOrigin: `${point.x}px ${point.y}px`,
                    duration: 2.1,
                    ease: 'sine.inOut',
                });
            }, controller.signal);
            if (id !== runId) return;
            move('threshold');
            interiorImg.src = image.src;
            interiorImg.hidden = false;
            gsap.set(interiorImg, { opacity: 0, scale: 1, transformOrigin: '50% 50%' });
            await animate((tl) => {
                tl.to(plane, { scale: '*=1.06', duration: 1.05, ease: 'sine.inOut' })
                    .to(interiorImg, { opacity: 1, duration: 1.05, ease: 'sine.inOut' }, 0)
                    .to(interiorImg, { scale: 1.18, duration: 1.6, ease: 'sine.out' }, 0);
            }, controller.signal);
            if (id !== runId) return;
            move('interior');
            status.textContent = '';
            welcome.hidden = false;
            gsap.fromTo(welcome, { opacity: 0, y: 10 },
                { opacity: 1, y: 0, duration: 0.5, ease: 'sine.out' });
            handoff.focus({ preventScroll: true });
        } catch (error) {
            if (id !== runId ||
                (error instanceof DOMException && error.name === 'AbortError')) return;
            fail();
        }
    };

    let scrollTween;
    const descendTo = (target) => {
        if (!target) return;
        if (quality.reduced) {
            target.scrollIntoView({ block: 'start' });
            target.focus({ preventScroll: true });
            return;
        }
        scrollTween?.kill();
        scrollTween = gsap.to(window, {
            duration: 0.85,
            ease: 'power2.inOut',
            scrollTo: { y: target, offsetY: 110, autoKill: true },
            onComplete: () => target.focus({ preventScroll: true }),
        });
    };

    root.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 ||
            event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const target = event.target instanceof Element ? event.target : null;

        const hotspot = target?.closest('[data-hotspot]');
        if (hotspot && root.contains(hotspot)) {
            if (quality.direct) return;
            const module = modules.find((m) => m.id === hotspot.dataset.hotspot);
            if (!module) return;
            event.preventDefault();
            void select(module, hotspot);
            return;
        }
        if (target?.closest('[data-return], [data-cancel]')) {
            event.preventDefault();
            reset(true, true);
            return;
        }
        if (target?.closest('[data-scroll-services]')) {
            const title = document.querySelector('#services-title');
            if (!title) return;
            event.preventDefault();
            descendTo(title);
        }
    });

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && machine.state !== 'street') reset(true, true);
    });

    let observedWidth = 0;
    const observer = new ResizeObserver((entries) => {
        const width = entries[0]?.contentRect.width ?? 0;
        if (Math.abs(width - observedWidth) < 1) return;
        observedWidth = width;
        if (machine.state !== 'street') reset(false, false);
        repositionHotspots();
    });
    observer.observe(stage);

    let introContext;
    const refreshIntro = () => {
        introContext?.revert();
        introContext = undefined;
        if (quality.reduced || !intro) return;
        introContext = gsap.context(() => {
            gsap.timeline({
                scrollTrigger: { trigger: root, start: 'top top', end: '65% top', scrub: true },
            }).to(intro, { opacity: 0, y: -32, ease: 'none', duration: 1 }, 0);
        }, root);
    };

    const media = gsap.matchMedia();
    media.add(
        {
            desktop: '(min-width: 601px)',
            compact: '(max-width: 600px)',
            reduce: '(prefers-reduced-motion: reduce)',
        },
        (context) => {
            quality.reduced = Boolean(context.conditions?.reduce);
            quality.compact = Boolean(context.conditions?.compact);
            if (quality.reduced && machine.state !== 'street') reset(false, false);
            refreshIntro();
        },
    );

    repositionHotspots();
    root.dataset.enhanced = 'true';
    root.dataset.sceneState = 'street';
})();
