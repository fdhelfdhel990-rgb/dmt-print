document.addEventListener('DOMContentLoaded', () => {
    const mobileButton = document.querySelector('[data-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    mobileButton?.addEventListener('click', () => {
        const open = mobileMenu.classList.toggle('open');
        mobileButton.setAttribute('aria-expanded', String(open));
    });

    const adminButton = document.querySelector('[data-admin-menu]');
    const adminSidebar = document.querySelector('[data-admin-sidebar]');
    adminButton?.addEventListener('click', () => adminSidebar.classList.toggle('open'));

    document.querySelectorAll('[data-tabs]').forEach((tabs) => {
        tabs.querySelectorAll('[data-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                const scope = tabs.parentElement;
                tabs.querySelectorAll('[data-tab]').forEach((item) => item.classList.remove('active'));
                scope.querySelectorAll('[data-panel]').forEach((panel) => panel.classList.remove('active'));
                button.classList.add('active');
                scope.querySelector(`[data-panel="${button.dataset.tab}"]`)?.classList.add('active');
            });
        });
    });

    document.querySelectorAll('[data-qty-plus]').forEach((button) => button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('[data-qty]');
        input.value = Number(input.value || 0) + 1;
    }));
    document.querySelectorAll('[data-qty-minus]').forEach((button) => button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('[data-qty]');
        input.value = Math.max(1, Number(input.value || 1) - 1);
    }));

    const slider = document.querySelector('[data-slider]');
    if (slider) {
        const slides = [...slider.querySelectorAll('.hero-slide')];
        const dots = [...slider.querySelectorAll('[data-slide]')];
        const previous = slider.querySelector('[data-slide-prev]');
        const next = slider.querySelector('[data-slide-next]');
        let active = Math.max(0, slides.findIndex((slide) => slide.classList.contains('active')));
        let timer = null;
        const show = (index) => {
            active = (index + slides.length) % slides.length;
            slides.forEach((slide, i) => slide.classList.toggle('active', i === active));
            dots.forEach((dot, i) => dot.classList.toggle('active', i === active));
        };
        const stop = () => {
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
        };
        const start = () => {
            if (slides.length < 2 || document.hidden) {
                return;
            }
            stop();
            timer = window.setInterval(() => show(active + 1), 6000);
        };
        const go = (index) => {
            show(index);
            start();
        };
        dots.forEach((dot) => dot.addEventListener('click', () => go(Number(dot.dataset.slide))));
        previous?.addEventListener('click', () => go(active - 1));
        next?.addEventListener('click', () => go(active + 1));
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
        let touchStart = null;
        slider.addEventListener('touchstart', (event) => {
            touchStart = event.touches[0]?.clientX ?? null;
        }, { passive: true });
        slider.addEventListener('touchend', (event) => {
            if (touchStart === null) {
                return;
            }
            const delta = (event.changedTouches[0]?.clientX ?? touchStart) - touchStart;
            if (Math.abs(delta) > 40) {
                go(active + (delta < 0 ? 1 : -1));
            }
            touchStart = null;
        });
        start();
    }

    document.querySelectorAll('[data-copy-button]').forEach((button) => {
        button.addEventListener('click', async () => {
            const source = button.closest('.payment-method-card')?.querySelector('[data-copy-source]');
            const feedback = document.querySelector('[data-copy-feedback]');
            if (!source) {
                return;
            }
            await navigator.clipboard?.writeText(source.textContent.trim());
            if (feedback) {
                feedback.textContent = 'Nomor rekening berhasil disalin';
            }
        });
    });
});
