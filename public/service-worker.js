const CACHE_NAME = 'that-car-app-shell-v3';
const SHELL = ['/offline.html', '/icons/app-icon-192.png', '/manifest.webmanifest'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET' || new URL(event.request.url).origin !== self.location.origin) {
        return;
    }

    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match('/offline.html')));
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => cached || fetch(event.request).then((response) => {
            if (response.ok && ['style', 'script', 'font', 'image'].includes(event.request.destination)) {
                const copy = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));
            }

            return response;
        })),
    );
});
