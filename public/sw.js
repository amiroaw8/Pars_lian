const CACHE_NAME = 'pars-lian-v4';

const ASSETS_TO_CACHE = ['/manifest.json'];

function isHtmlNavigation(request) {
    if (request.mode === 'navigate') {
        return true;
    }

    return (request.headers.get('accept') || '').includes('text/html');
}

function isStaticAsset(pathname) {
    return (
        pathname.startsWith('/build/') ||
        pathname.startsWith('/css/') ||
        pathname.startsWith('/js/') ||
        pathname.startsWith('/fonts/') ||
        pathname.startsWith('/vendor/') ||
        pathname.startsWith('/assets/') ||
        pathname.startsWith('/images/') ||
        pathname.endsWith('.woff2') ||
        pathname.endsWith('.woff') ||
        pathname.endsWith('.css') ||
        pathname.endsWith('.js') ||
        pathname.endsWith('.png') ||
        pathname.endsWith('.svg') ||
        pathname.endsWith('.ico')
    );
}

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) =>
            cache.addAll(ASSETS_TO_CACHE).catch(() => undefined)
        )
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) =>
                Promise.all(
                    cacheNames
                        .filter((name) => name !== CACHE_NAME)
                        .map((name) => caches.delete(name))
                )
            )
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    // صفحات HTML و مسیرهای Laravel را SW دست نمی‌زند (جلوگیری از Failed to fetch)
    if (isHtmlNavigation(event.request)) {
        return;
    }

    const url = new URL(event.request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (!isStaticAsset(url.pathname)) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            const networkFetch = fetch(event.request)
                .then((response) => {
                    if (response && response.status === 200 && response.type === 'basic') {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    }

                    return response;
                })
                .catch(() => cached);

            return cached || networkFetch;
        })
    );
});
