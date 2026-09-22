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
        const show = (index) => {
            slides.forEach((slide, i) => slide.classList.toggle('active', i === index));
            dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
        };
        dots.forEach((dot) => dot.addEventListener('click', () => show(Number(dot.dataset.slide))));
        let active = 0;
        window.setInterval(() => { active = (active + 1) % slides.length; show(active); }, 6000);
    }
});
