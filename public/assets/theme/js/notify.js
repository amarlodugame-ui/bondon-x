(() => {
    const defaults = { success: 'check', error: 'error', warning: 'warning', info: 'info', special: 'gift' };
    const icons = ['cart', 'check', 'lock', 'warning', 'gift', 'x', 'error', 'info', 'order', 'handbag'];

    function mount(card, duration = 5000) {
        const stack = document.querySelector('[data-notify-stack]');
        if (!stack || card.hasAttribute('data-notify-ready')) return;
        stack.append(card);
        card.setAttribute('data-notify-ready', '');

        let remaining = duration;
        let startedAt;
        let timer;
        let closing = false;
        let paused = true;
        const progress = card.querySelector('.notify-progress');
        if (progress) {
            progress.hidden = duration === 0;
            progress.querySelector('span').style.animationDuration = duration + 'ms';
        }

        const dismiss = () => {
            if (closing) return;
            closing = true;
            clearTimeout(timer);
            card.classList.add('notify-vanish');
            setTimeout(() => card.remove(), 220);
        };
        const resume = () => {
            if (closing || !paused || duration === 0 || card.matches(':hover, :focus-within')) return;
            paused = false;
            card.classList.remove('notify-paused');
            startedAt = performance.now();
            timer = setTimeout(dismiss, remaining);
        };
        const pause = () => {
            if (paused || closing) return;
            paused = true;
            clearTimeout(timer);
            remaining = Math.max(0, remaining - (performance.now() - startedAt));
            card.classList.add('notify-paused');
        };

        card.querySelector('[data-notify-close]').addEventListener('click', dismiss);
        card.addEventListener('mouseenter', pause);
        card.addEventListener('mouseleave', resume);
        card.addEventListener('focusin', pause);
        card.addEventListener('focusout', () => setTimeout(resume, 0));
        resume();
        return { dismiss };
    }

    window.notify = (options = {}) => {
        options ??= {};
        if (typeof options === 'string') options = { message: options };
        const type = Object.hasOwn(defaults, options.type) ? options.type : 'info';
        const icon = icons.includes(options.icon) ? options.icon : defaults[type];
        const template = document.querySelector(`[data-notify-template="${type}-${icon}"]`)
            || document.querySelector(`[data-notify-template="${type}-${defaults[type]}"]`);
        if (!template) return;

        const card = template.content.firstElementChild.cloneNode(true);
        card.setAttribute('data-notify', '');
        card.setAttribute('data-notify-type', type);
        if (options.title != null) card.querySelector('.notify-copy h3').textContent = String(options.title);
        card.querySelector('.notify-copy p').textContent = String(options.message ?? '');
        const symbol = card.querySelector('.notify-icon-disc use');
        if (symbol) symbol.setAttribute('href', '#' + icon);
        const duration = Number.isFinite(options.duration) && options.duration >= 0 ? options.duration : 5000;
        return mount(card, duration);
    };

    window.notify.redirect = (url, options) => {
        const target = new URL(url, location.href);
        if (target.origin === location.origin) {
            try {
                sessionStorage.setItem('bondon.notify.redirect', JSON.stringify({
                    path: target.pathname,
                    expiresAt: Date.now() + 60000,
                    options,
                }));
            } catch (_) {}
        }
        location.assign(target.href);
    };

    const initialize = () => {
        document.querySelectorAll('[data-notify]:not([data-notify-ready])').forEach(card => mount(card));
        try {
            const pending = JSON.parse(sessionStorage.getItem('bondon.notify.redirect') || 'null');
            sessionStorage.removeItem('bondon.notify.redirect');
            if (pending?.path === location.pathname && pending.expiresAt > Date.now()) {
                window.notify(pending.options);
            }
        } catch (_) {}
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
