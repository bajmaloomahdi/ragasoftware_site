import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

Alpine.plugin(collapse);
Alpine.plugin(focus);

window.Alpine = Alpine;
Alpine.start();

/* ------------------------------------------------------------------ */
/*  Scrollspy — highlights the header nav link of the section in view.
/*  Nav links opt in with data-spy="<anchor-id>". No-op elsewhere.
/* ------------------------------------------------------------------ */
function initScrollspy() {
    const links = Array.from(document.querySelectorAll('.nav-link[data-spy]'));
    if (!links.length) return;

    const byId = new Map(links.map((el) => [el.dataset.spy, el]));
    const targets = [...byId.keys()]
        .map((id) => document.getElementById(id))
        .filter(Boolean);
    if (!targets.length) return;

    let current = null;
    const setActive = (id) => {
        if (id === current) return;
        current = id;
        links.forEach((el) => el.classList.toggle('is-active', el.dataset.spy === id));
    };

    const observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((e) => e.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
            if (visible) setActive(visible.target.id);
        },
        { rootMargin: '-45% 0px -50% 0px', threshold: [0, 0.25, 0.5, 1] },
    );

    targets.forEach((t) => observer.observe(t));
}

if (document.readyState !== 'loading') initScrollspy();
else document.addEventListener('DOMContentLoaded', initScrollspy);

/* ------------------------------------------------------------------ */
/*  Image fade-in — every <img class="img-fade"> reveals itself once it
/*  has actually loaded, instead of popping in as the network delivers it.
/* ------------------------------------------------------------------ */
function initImageFadeIn() {
    document.querySelectorAll('img.img-fade').forEach((img) => {
        const reveal = () => img.classList.add('is-loaded');
        if (img.complete) reveal();
        else {
            img.addEventListener('load', reveal, { once: true });
            img.addEventListener('error', reveal, { once: true });
        }
    });
}

if (document.readyState !== 'loading') initImageFadeIn();
else document.addEventListener('DOMContentLoaded', initImageFadeIn);
