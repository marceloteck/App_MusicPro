self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open("offline-cache").then((cache) => {
            return cache.addAll([
                "/", // Página inicial
                "/offline", // Página offline personalizada
                "/css/style.css",
                "/js/app.js",
                "/Assets",
            ]);
        })
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        fetch(event.request).catch(() => caches.match("/offline.html"))
    );
});
