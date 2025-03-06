// Nombre del caché
const CACHE_NAME = 'banco-camba-v1';

// Archivos a cachear
const urlsToCache = [
  '/',
  '/index.php',
  '/assets/css/styles.css',
  '/assets/js/main.js',
  '/assets/img/logo.png',
  '/assets/img/bandera.jpg',
  '/assets/img/imgSantaCruz.jpg',
  '/manifest.json'
];

// Instalación del Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache abierto');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activación del Service Worker
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Estrategia de caché: Network first, fallback to cache
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Si la respuesta es válida, la clonamos y la guardamos en el caché
        if (event.request.method === 'GET' && response && response.status === 200) {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME)
            .then(cache => {
              cache.put(event.request, responseToCache);
            });
        }
        return response;
      })
      .catch(() => {
        // Si la red falla, intentamos recuperar desde el caché
        return caches.match(event.request);
      })
  );
});

// Sincronización en segundo plano
self.addEventListener('sync', event => {
  if (event.tag === 'sync-transactions') {
    event.waitUntil(syncPendingTransactions());
  }
});

// Función para sincronizar transacciones pendientes
function syncPendingTransactions() {
  return new Promise((resolve, reject) => {
    // Aquí iría la lógica para sincronizar transacciones pendientes
    // cuando el usuario recupera la conexión a internet
    resolve();
  });
}