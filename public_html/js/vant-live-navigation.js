(() => {
    if (window.VantLiveNavigationInitialized) {
        return;
    }

    window.VantLiveNavigationInitialized = true;

    const pageSelector = '[data-vant-page]';
    const excludedPathPrefixes = ['/admin', '/login', '/register', '/dashboard', '/logout'];
    const excludedExtensions = ['.pdf', '.jpg', '.jpeg', '.png', '.webp', '.svg', '.zip'];

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const saveScroll = () => {
        const state = { ...(history.state || {}), vant: true, scroll: { x: window.scrollX, y: window.scrollY } };
        history.replaceState(state, document.title, window.location.href);
    };

    const isExcludedUrl = (url) => {
        const pathname = url.pathname.toLowerCase();

        return excludedPathPrefixes.some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`))
            || excludedExtensions.some((extension) => pathname.endsWith(extension));
    };

    const shouldHandle = (event, anchor) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return false;
        }

        if (!anchor || anchor.target === '_blank' || anchor.hasAttribute('download') || anchor.dataset.noLive !== undefined) {
            return false;
        }

        const url = new URL(anchor.href, window.location.href);

        if (url.origin !== window.location.origin || isExcludedUrl(url)) {
            return false;
        }

        if (url.pathname === window.location.pathname && url.search === window.location.search) {
            return false;
        }

        return true;
    };

    const updateActiveLinks = () => {
        document.querySelectorAll('a[href]').forEach((anchor) => {
            const url = new URL(anchor.href, window.location.href);

            if (url.origin !== window.location.origin) {
                return;
            }

            const active = url.pathname === window.location.pathname;
            anchor.classList.toggle('text-vant-gold', active);
        });
    };

    const initPage = (main) => {
        if (window.Alpine && typeof window.Alpine.initTree === 'function') {
            window.Alpine.initTree(main);
        }
    };

    const loadPage = async (url, options = {}) => {
        document.body.classList.add('vant-loading');

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Leivant-Live-Navigation': 'true',
                },
            });

            if (!response.ok) {
                throw new Error(`Navigation failed with status ${response.status}`);
            }

            const html = await response.text();
            const nextDocument = new DOMParser().parseFromString(html, 'text/html');
            const nextMain = nextDocument.querySelector(pageSelector);
            const currentMain = document.querySelector(pageSelector);

            if (!nextMain || !currentMain) {
                throw new Error('Page shell was not found.');
            }

            currentMain.replaceWith(nextMain);
            document.title = nextDocument.title || document.title;

            if (options.push !== false) {
                const scroll = options.preserveScroll ? { x: window.scrollX, y: window.scrollY } : { x: 0, y: 0 };
                history.pushState({ vant: true, scroll }, document.title, url);

                if (!options.preserveScroll) {
                    window.scrollTo(0, 0);
                }
            } else if (options.scroll) {
                window.scrollTo(options.scroll.x || 0, options.scroll.y || 0);
            }

            initPage(nextMain);
            updateActiveLinks();
            window.dispatchEvent(new CustomEvent('vant:navigated', { detail: { url } }));
        } catch (error) {
            window.location.href = url;
        } finally {
            document.body.classList.remove('vant-loading');
        }
    };

    document.addEventListener('click', (event) => {
        const anchor = event.target.closest('a[href]');

        if (!shouldHandle(event, anchor)) {
            return;
        }

        event.preventDefault();
        saveScroll();
        loadPage(anchor.href);
    });

    const cleanFormUrl = (form) => {
        const url = new URL(form.action || window.location.href, window.location.href);
        const data = new FormData(form);

        url.search = '';
        data.forEach((value, key) => {
            if (String(value).trim() !== '') {
                url.searchParams.append(key, value);
            }
        });

        return url;
    };

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-auto-filter]');

        if (!form || String(form.method).toLowerCase() !== 'get') {
            return;
        }

        event.preventDefault();
        saveScroll();
        loadPage(cleanFormUrl(form).href, { preserveScroll: true });
    });

    const submitTimers = new WeakMap();
    const autoSubmit = (form, delay = 350) => {
        const status = form.querySelector('[data-auto-filter-status]');
        clearTimeout(submitTimers.get(form));

        if (status) {
            status.classList.remove('hidden');
        }

        submitTimers.set(form, setTimeout(() => {
            if (form.requestSubmit) {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
        }, delay));
    };

    document.addEventListener('input', (event) => {
        const form = event.target.closest('form[data-auto-filter]');

        if (!form || !event.target.matches('input[type="search"], input[type="number"]')) {
            return;
        }

        autoSubmit(form, 450);
    });

    document.addEventListener('change', (event) => {
        const form = event.target.closest('form[data-auto-filter]');

        if (!form || !event.target.matches('select, input[type="checkbox"], input[type="radio"]')) {
            return;
        }

        autoSubmit(form, 0);
    });

    window.addEventListener('popstate', (event) => {
        loadPage(window.location.href, {
            push: false,
            scroll: event.state?.scroll || { x: 0, y: 0 },
        });
    });

    saveScroll();
})();
