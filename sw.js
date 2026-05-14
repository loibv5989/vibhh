const CACHE_NAME = 'vibhh-v1';

const urlsToCache = [
    '/'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
    self.skipWaiting();
});

self.addEventListener('fetch', event => {
    const {request} = event;
    const url = new URL(request.url);

    if (url.pathname.match(/\.(json|php)$/) && url.pathname !== '/manifest.json') {
        return;
    }

    if (url.origin !== location.origin && url.origin !== 'https://cdn.nbblo.top') {
        return;
    }

    if (request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image') {

        event.respondWith(
            caches.match(request).then(cached => {
                return cached || fetch(request).then(response => {
                    if (response && response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseClone);
                        });
                    }
                    return response;
                });
            })
        );
    } else {
        event.respondWith(
            fetch(request).catch(() => caches.match(request) || new Response())
        );
    }
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(c => c !== CACHE_NAME).map(c => caches.delete(c))
            );
        })
    );
    self.clients.claim();
});
