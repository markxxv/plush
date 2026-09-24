import Alpine from 'alpinejs'
import EmblaCarousel from 'embla-carousel'

window.Alpine = Alpine

let CookieConsent = null;

const cookieConsentReady = Promise.all([
    import('vanilla-cookieconsent'),
    import('../css/cookieconsent.css'),
]).then(([module]) => {
    CookieConsent = module;

    return module;
});

const META_PIXEL_ID = '2751219188608886';
const GA_ID = 'G-S1M9Q3EGJ9';
const CLARITY_ID = 'qjix4dki1n';


function showToast(message, label = null) {
    document.querySelector('.alert_box')?.remove();

    const template = document.getElementById('storefront-toast-template');
    if (!template) return;

    const toast = document.createElement('div');
    toast.className = 'alert_box';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    toast.append(template.content.cloneNode(true));

    const messageEl = toast.querySelector('[data-toast-message]');
    const labelEl = toast.querySelector('[data-toast-label]');
    const close = toast.querySelector('[data-toast-close]');

    if (messageEl) {
        messageEl.textContent = message;
    }

    if (label && labelEl) {
        labelEl.textContent = label;
    }

    document.body.appendChild(toast);

    let removed = false;

    const removeToast = () => {
        if (removed) return;
        removed = true;

        toast.classList.remove('is-visible');
        toast.classList.add('is-leaving');

        window.setTimeout(() => toast.remove(), 220);
    };

    close?.addEventListener('click', removeToast);

    requestAnimationFrame(() => {
        requestAnimationFrame(() => toast.classList.add('is-visible'));
    });

    window.setTimeout(removeToast, 5000);
}

function initDeferredHeroVideo() {
    const video = document.querySelector('[data-hero-video]');
    if (!video) return;

    const loadAndPlay = () => {
        const source = video.querySelector('source[data-src]');
        if (!source) return;

        source.src = source.dataset.src;
        source.removeAttribute('data-src');

        video.load();

        const play = video.play();

        play?.catch(() => {
            // Poster remains visible if autoplay is unavailable.
        });
    };

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(loadAndPlay, { timeout: 1200 });
        return;
    }

    window.setTimeout(loadAndPlay, 600);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDeferredHeroVideo, { once: true });
} else {
    initDeferredHeroVideo();
}

document.addEventListener('alpine:init', () => {
    Alpine.data('preorderRequest', (config) => ({
        open: false,
        sending: false,
        error: '',
        fields: {
            fullName: '',
            phone: '',
            email: '',
            measurements: '',
            comment: '',
        },

        reset() {
            this.fields = {
                fullName: '',
                phone: '',
                email: '',
                measurements: '',
                comment: '',
            };
            this.error = '';
        },

        async submit() {
            if (this.sending) return;

            this.error = '';

            if (!this.fields.fullName.trim()) {
                this.error = config.nameError;
                return;
            }

            if (!this.fields.phone.trim() && !this.fields.email.trim()) {
                this.error = config.contactError;
                return;
            }

            this.sending = true;

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch('/api/request', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf || '',
                    },
                    body: JSON.stringify({
                        product_id: config.productId,
                        locale: config.locale,
                        full_name: this.fields.fullName.trim(),
                        phone: this.fields.phone.trim(),
                        email: this.fields.email.trim(),
                        measurements: this.fields.measurements.trim(),
                        comment: this.fields.comment.trim(),
                    }),
                });

                if (!response.ok) {
                    this.error = config.sendError;
                    return;
                }

                this.open = false;
                this.reset();
                showToast(config.successMessage, config.successLabel);
            } catch {
                this.error = config.sendError;
            } finally {
                this.sending = false;
            }
        },
    }));

    Alpine.data('productGallery', (totalSlides, images) => ({
        activeSlide: 0,
        totalSlides,
        images,
        isZoomed: false,
        isDragging: false,
        zoomPosition: { x: 0.5, y: 0.5 },
        embla: null,
        pointerActive: false,
        pointerStartX: 0,
        pointerStartY: 0,
        pointerMoved: false,

        init() {
            this.$nextTick(() => {
                if (!this.$refs.viewport) return;

                this.embla = EmblaCarousel(this.$refs.viewport, {
                    align: 'start',
                    containScroll: 'trimSnaps',
                    loop: false,
                    dragFree: false,
                    duration: 25,
                    watchDrag: () => !this.isZoomed,
                });

                const syncSelected = () => {
                    this.activeSlide = this.embla.selectedScrollSnap();
                    this.scrollThumbnailIntoView();
                };

                this.embla.on('select', syncSelected);
                this.embla.on('reInit', syncSelected);
                this.embla.on('pointerDown', () => {
                    this.isDragging = true;
                });
                this.embla.on('pointerUp', () => {
                    this.isDragging = false;
                });

                syncSelected();
            });
        },

        destroy() {
            this.embla?.destroy();
            this.embla = null;
        },

        goPrev() {
            this.embla?.scrollPrev();
        },

        goNext() {
            this.embla?.scrollNext();
        },

        goTo(index) {
            if (!this.embla || this.totalSlides < 1) return;

            const clamped = Math.max(0, Math.min(this.totalSlides - 1, index));
            this.embla.scrollTo(clamped);
        },

        recordPointerDown(event) {
            if (this.isZoomed) return;

            this.pointerActive = true;
            this.pointerMoved = false;
            this.pointerStartX = event.clientX;
            this.pointerStartY = event.clientY;
        },

        recordPointerUp(event) {
            if (!this.pointerActive) return;

            const dx = Math.abs(event.clientX - this.pointerStartX);
            const dy = Math.abs(event.clientY - this.pointerStartY);

            this.pointerMoved = dx > 6 || dy > 6;
            this.pointerActive = false;
        },

        handleClick() {
            if (this.pointerMoved || this.isDragging) {
                this.pointerMoved = false;
                return;
            }

            this.isZoomed = !this.isZoomed;
        },

        scrollThumbnailIntoView() {
            this.$nextTick(() => {
                const container = this.$refs.thumbScroller;
                if (!container) return;

                const activeButton = container.querySelector(
                    `[data-thumb-index="${this.activeSlide}"]`
                );

                if (!activeButton) return;

                const containerRect = container.getBoundingClientRect();
                const buttonRect = activeButton.getBoundingClientRect();

                if (
                    buttonRect.left < containerRect.left ||
                    buttonRect.right > containerRect.right
                ) {
                    activeButton.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'nearest',
                    });
                }
            });
        },

        handleMouseMove(event) {
            if (!this.isZoomed) return;

            const bounds = event.currentTarget.getBoundingClientRect();

            this.zoomPosition = {
                x: (event.clientX - bounds.left) / bounds.width,
                y: (event.clientY - bounds.top) / bounds.height,
            };
        },
    }));

    Alpine.store('cart', {
        items: [],
        isOpen: false,

        get count() {
            return this.items.reduce((sum, item) => {
                return sum + (item.quantity ?? 1);
            }, 0);
        },

        init() {
            const stored = localStorage.getItem('nd_cart');
            if (stored) {
                this.items = JSON.parse(stored);
            }
        },

        save() {
            localStorage.setItem('nd_cart', JSON.stringify(this.items));
        },

        addItem(product, colorId = null, sizeId = null, quantity = 1) {
            const itemId = `${product.id}_${colorId || 'null'}_${sizeId || 'null'}`;
            const existing = this.items.find(item => item.id === itemId);

            if (existing) {
                existing.quantity += quantity;
            } else {
                this.items.push({
                    id: itemId,
                    product_id: product.id,
                    product: product,
                    color_id: colorId,
                    size_id: sizeId,
                    quantity: quantity
                });
            }

            this.save();
            this.showNotification(`${product.title} added to cart`);

            window.metaTrack?.('AddToCart', {
                content_type: 'product',
                content_ids: [String(product.id)],
                content_name: product.title,
                content_category: product.category || '',
                value: Number(product.price) * Number(quantity),
                currency: 'EUR',
            });
        },

        removeItem(itemId) {
            this.items = this.items.filter(item => item.id !== itemId);
            this.save();
        },

        updateQuantity(itemId, newQuantity) {
            if (newQuantity <= 0) {
                this.removeItem(itemId);
                return;
            }

            const item = this.items.find(item => item.id === itemId);
            if (item) {
                item.quantity = newQuantity;
                this.save();
            }
        },

        clearCart() {
            this.items = [];
            this.save();
        },

        get totalItems() {
            return this.items.reduce((total, item) => total + item.quantity, 0);
        },

        get totalPrice() {
            return this.items.reduce((total, item) => total + (item.product.price * item.quantity), 0);
        },

        toggleCart() {
            this.isOpen = !this.isOpen;
        },

        showNotification(message) {
            showToast(message);
        },

        // getColorName(colors, colorId) {
        //     const color = colors.find(c => c.id == colorId);
        //     return color ? color.title : '';
        // },

        getSizeName(sizes, sizeId) {
            const size = sizes.find(s => s.id == sizeId);
            return size ? size.title : '';
        }

    });

    Alpine.data('productPage', (product) => ({
        selectedSizeId: product.sizes.length === 1 ? product.sizes[0].id : null,
        selectedColorId: null,
        quantity: 1,
        errors: {},
        quickBuyOpen: false,
        quickBuyVisible: false,
        quickBuyCheck: null,

        init() {
            const hero = document.querySelector('[data-product-hero]');
            if (!hero) return;

            this.quickBuyCheck = () => {
                this.quickBuyVisible =
                    hero.getBoundingClientRect().bottom <= window.innerHeight - 100;

                if (!this.quickBuyVisible) {
                    this.quickBuyOpen = false;
                }
            };

            this.quickBuyCheck();

            window.addEventListener('scroll', this.quickBuyCheck, { passive: true });
            window.addEventListener('resize', this.quickBuyCheck);
        },

        destroy() {
            if (!this.quickBuyCheck) return;

            window.removeEventListener('scroll', this.quickBuyCheck);
            window.removeEventListener('resize', this.quickBuyCheck);
        },

        addToCart() {
            this.errors = {};

            if (product.sizes.length > 1 && !this.selectedSizeId) {
                this.errors.size = 'Select size';
            }

            if (Object.keys(this.errors).length > 0) return false;

            this.$store.cart.addItem(
                product,
                this.selectedColorId,
                this.selectedSizeId,
                this.quantity
            );

            return true;
        },

        openQuickBuy() {
            if (product.sizes.length > 1) {
                this.errors = {};
                this.quickBuyOpen = true;
                return;
            }

            this.addToCart();
        },

        confirmQuickBuy() {
            if (this.addToCart()) {
                this.quickBuyOpen = false;
            }
        }
    }));

    Alpine.store('wishlist', {
        items: [],
        isOpen: false,

        init() {
            const stored = localStorage.getItem('nd_wishlist');
            if (stored) {
                this.items = JSON.parse(stored);
            }
        },

        save() {
            localStorage.setItem('nd_wishlist', JSON.stringify(this.items));
        },

        addItem(product) {
            const existing = this.items.find(item => item.id === product.id);

            if (!existing) {
                this.items.push({
                    id: product.id,
                    product: product,
                    added_at: new Date().toISOString()
                });

                this.save();
                this.showNotification(`${product.title} added to wishlist`);
            }
        },

        removeItem(productId) {
            this.items = this.items.filter(item => item.id !== productId);
            this.save();
        },

        isInWishlist(productId) {
            return this.items.some(item => item.id === productId);
        },

        toggleItem(product) {
            if (this.isInWishlist(product.id)) {
                this.removeItem(product.id);
            } else {
                this.addItem(product);
            }
        },

        get totalItems() {
            return this.items.length;
        },

        toggleWishlist() {
            this.isOpen = !this.isOpen;
        },

        showNotification(message) {
            showToast(message);
        }
    });

});




Alpine.start()


const pendingMetaEvents = [];

function loadMetaPixel() {
    if (window.__metaPixelLoaded) {
        return;
    }

    window.__metaPixelLoaded = true;

    !function (f, b, e, v, n, t, s) {
        if (f.fbq) return;

        n = f.fbq = function () {
            n.callMethod
                ? n.callMethod.apply(n, arguments)
                : n.queue.push(arguments);
        };

        if (!f._fbq) f._fbq = n;

        n.push = n;
        n.loaded = true;
        n.version = '2.0';
        n.queue = [];

        t = b.createElement(e);
        t.async = true;
        t.src = v;

        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s);
    }(
        window,
        document,
        'script',
        'https://connect.facebook.net/en_US/fbevents.js'
    );

    window.fbq('init', META_PIXEL_ID);
    window.fbq('track', 'PageView');
}

function metaEventWasSent(uniqueKey) {
    if (!uniqueKey) {
        return false;
    }

    try {
        return localStorage.getItem(uniqueKey) === '1';
    } catch {
        return false;
    }
}

function rememberMetaEvent(uniqueKey) {
    if (!uniqueKey) {
        return;
    }

    try {
        localStorage.setItem(uniqueKey, '1');
    } catch {
        //
    }
}

function sendMetaEvent(event, parameters = {}, uniqueKey = null) {
    if (metaEventWasSent(uniqueKey)) {
        return;
    }

    loadMetaPixel();

    window.fbq('track', event, parameters);

    rememberMetaEvent(uniqueKey);
}

window.metaTrack = function (
    event,
    parameters = {},
    uniqueKey = null
) {
    if (!CookieConsent) {
        pendingMetaEvents.push({
            event,
            parameters,
            uniqueKey,
        });

        return;
    }

    if (CookieConsent.acceptedCategory('marketing')) {
        sendMetaEvent(event, parameters, uniqueKey);

        return;
    }

    // Сохраняем событие только пока пользователь ещё не сделал выбор.
    if (!CookieConsent.validConsent()) {
        pendingMetaEvents.push({
            event,
            parameters,
            uniqueKey,
        });
    }
};

function syncMetaPixelConsent() {
    if (CookieConsent.acceptedCategory('marketing')) {
        loadMetaPixel();

        pendingMetaEvents
            .splice(0)
            .forEach(({ event, parameters, uniqueKey }) => {
                sendMetaEvent(event, parameters, uniqueKey);
            });

        return;
    }

    pendingMetaEvents.length = 0;

    // Пользователь отозвал ранее выданное согласие.
    if (window.__metaPixelLoaded) {
        window.location.reload();
    }
}

function loadGA() {
    if (window.__gaLoaded) {
        return;
    }

    window.__gaLoaded = true;

    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };

    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_ID}`;
    document.head.appendChild(script);

    window.gtag('js', new Date());
    window.gtag('config', GA_ID, { anonymize_ip: true });
}

function loadClarity() {
    if (window.__clarityLoaded) {
        return;
    }

    window.__clarityLoaded = true;

    (function (c, l, a, r, i, t, y) {
        c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); };
        t = l.createElement(r); t.async = 1; t.src = "https://www.clarity.ms/tag/" + i;
        y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
    })(window, document, "clarity", "script", CLARITY_ID);
}

function syncGAConsent() {
    if (CookieConsent.acceptedCategory('analytics')) {
        loadGA();
        loadClarity();
        return;
    }  

    // Пользователь отозвал ранее выданное согласие.
    if (window.__gaLoaded) {
        window.location.reload();
    }
}

// Cookies Banner
document.addEventListener("DOMContentLoaded", async () => {
    const consent = await cookieConsentReady;

    consent.run({
        mode: 'opt-in',

        // Увеличивай число при существенном изменении политики.
        revision: 1,

        guiOptions: {
            consentModal: {
                layout: 'box inline',
                position: 'bottom right',
                equalWeightButtons: true,
                flipButtons: true,
            },

            preferencesModal: {
                layout: 'box',
                position: 'right',
                equalWeightButtons: true,
                flipButtons: false,
            },
        },

        cookie: {
            name: 'cc_cookie',
            expiresAfterDays: 180,
            sameSite: 'Lax',
        },

        categories: {
            necessary: {
                readOnly: true,
            },

            analytics: {
                autoClear: {
                    cookies: [
                        { name: /^_ga$/ },
                        { name: /^_ga_/ },
                        { name: /^_gid$/ },
                    ],
                    reloadPage: false,
                },
            },

            marketing: {
                autoClear: {
                    cookies: [
                        {
                            name: /^_fbp$/,
                        },
                        {
                            name: /^_fbc$/,
                        },
                    ],
                    reloadPage: false,
                },
            },
        },

        onConsent: () => {
            syncMetaPixelConsent();
            syncGAConsent();
        },

        onChange: ({ changedCategories }) => {
            if (changedCategories.includes('marketing')) {
                syncMetaPixelConsent();
            }
            if (changedCategories.includes('analytics')) {
                syncGAConsent();
            }
        },

        language: {
            default: 'en',
            autoDetect: 'document',

            translations: {
                en: {
                    consentModal: {
                        title: 'Manage Cookie Consent',
                        description:
                            'We use necessary cookies for the website and optional marketing cookies for personalised advertising. Read our <a class="cc__link" href="/fr/cookies-policy">cookies policy</a>.',
                        acceptAllBtn: 'Accept all',
                        acceptNecessaryBtn: 'Reject all',
                        showPreferencesBtn: 'Manage preferences',
                        footer:
                            '<a href="/privacy-policy">Privacy Policy</a>' +
                            '<a href="/cookies-policy">Cookies Policy</a>',
                    },

                    preferencesModal: {
                        title: 'Consent Preferences',
                        acceptAllBtn: 'Accept all',
                        acceptNecessaryBtn: 'Reject all',
                        savePreferencesBtn: 'Save preferences',
                        closeIconLabel: 'Close',
                        serviceCounterLabel: 'Service|Services',

                        sections: [
                            {
                                title: 'Cookie Usage',
                                description:
                                    'Choose which optional cookies Maison Plush may use. You can change your choice at any time.',
                            },
                            {
                                title:
                                    'Strictly Necessary Cookies ' +
                                    '<span class="pm__badge">Always enabled</span>',
                                description:
                                    'These cookies are required for the cart, wishlist, checkout and other essential website functions.',
                                linkedCategory: 'necessary',
                            },
                            {
                                title: 'Marketing Cookies',
                                description:
                                    'Meta Pixel measures product views, cart actions and purchases, and helps us display relevant advertising on Facebook and Instagram.',
                                linkedCategory: 'marketing',
                            },
                            {
                                title: 'Analytics Cookies',
                                description:
                                    'Google Analytics helps us understand how visitors use the site, so we can improve navigation and content.',
                                linkedCategory: 'analytics',
                            },
                            {
                                title: 'More information',
                                description:
                                    'Read our <a class="cc__link" href="/cookies-policy">cookies policy</a>.',
                            },
                        ],
                    },
                },

                fr: {
                    consentModal: {
                        title: 'Gestion du consentement aux cookies',
                        description:
                            'Nous utilisons des cookies nécessaires au fonctionnement du site et des cookies marketing facultatifs pour la publicité personnalisée. Consultez notre <a class="cc__link" href="/fr/cookies-policy">politique de gestion des cookies</a>.',
                        acceptAllBtn: 'Tout accepter',
                        acceptNecessaryBtn: 'Tout refuser',
                        showPreferencesBtn: 'Gérer les préférences',
                        footer:
                            '<a href="/fr/privacy-policy">Politique de confidentialité</a>' +
                            '<a href="/fr/cookies-policy">Politique des cookies</a>',
                    },

                    preferencesModal: {
                        title: 'Préférences de consentement',
                        acceptAllBtn: 'Tout accepter',
                        acceptNecessaryBtn: 'Tout refuser',
                        savePreferencesBtn: 'Enregistrer les préférences',
                        closeIconLabel: 'Fermer',
                        serviceCounterLabel: 'Service|Services',

                        sections: [
                            {
                                title: 'Utilisation des cookies',
                                description:
                                    'Choisissez les cookies facultatifs que Maison Plush peut utiliser. Vous pouvez modifier votre choix à tout moment.',
                            },
                            {
                                title:
                                    'Cookies strictement nécessaires ' +
                                    '<span class="pm__badge">Toujours activés</span>',
                                description:
                                    'Ces cookies sont indispensables au panier, aux favoris, au paiement et aux autres fonctions essentielles du site.',
                                linkedCategory: 'necessary',
                            },
                            {
                                title: 'Cookies analytiques',
                                description:
                                    'Google Analytics nous aide à comprendre comment les visiteurs utilisent le site, afin d’améliorer la navigation et le contenu.',
                                linkedCategory: 'analytics',
                            },
                            {
                                title: 'Cookies marketing',
                                description:
                                    'Le Meta Pixel mesure les consultations de produits, les ajouts au panier et les achats, et nous aide à afficher des publicités pertinentes sur Facebook et Instagram.',
                                linkedCategory: 'marketing',
                            },
                            {
                                title: 'Plus d’informations',
                                description:
                                    'Consultez notre <a class="cc__link" href="/fr/cookies-policy">politique de gestion des cookies</a>.',
                            },
                        ],
                    },
                },
            },
        },

    });
});

