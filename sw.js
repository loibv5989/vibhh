const CACHE_NAME = 'vibhh-v2';
const urlsToCache = ['/'];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames =>
            Promise.all(cacheNames.filter(c => c !== CACHE_NAME).map(c => caches.delete(c)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET') return;

    if (url.pathname.endsWith('.php') || url.pathname.startsWith('/wp-json/') || (url.pathname.endsWith('.json') && url.pathname !== '/manifest.json')) {
        return;
    }

    if (url.origin !== location.origin && url.origin !== 'https://cdn.vibhh.com') {
        return;
    }

    if (request.destination === 'style' || request.destination === 'script' || request.destination === 'image'
    ) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;

                return fetch(request).then(response => {
                    if (response && response.status === 200 && response.type === 'basic') {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, clone);
                        });
                    }
                    return response;
                });
            })
        );
        return;
    }

    event.respondWith(
        fetch(request).catch(async () => {
            const cached = await caches.match(request);
            return cached ?? Response.error();
        })
    );
});
