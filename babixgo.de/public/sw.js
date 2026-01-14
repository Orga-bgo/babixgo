/**
 * Service Worker für babixGO.de PWA
 * Caching-Strategie: Cache-First für statische Assets, Network-First für HTML
 */

const CACHE_NAME = 'babixgo-v1';
const OFFLINE_URL = '/offline.html';

// Assets die beim Installation gecacht werden sollen
const PRECACHE_ASSETS = [
  '/',
  '/offline.html',
  '/assets/css/style.css',
  '/assets/js/main.js',
  '/assets/logo/babixGO-logo-hell.png'
];

// Installation: Precache kritische Assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(PRECACHE_ASSETS);
      })
      .then(() => self.skipWaiting())
  );
});

// Activation: Cleanup alter Caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((cacheName) => cacheName !== CACHE_NAME)
          .map((cacheName) => caches.delete(cacheName))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch: Caching-Strategien
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Nur eigene Domain cachen
  if (url.origin !== location.origin) {
    return;
  }

  // Strategie basierend auf Dateityp
  if (request.method === 'GET') {
    if (
      url.pathname.endsWith('.css') ||
      url.pathname.endsWith('.js') ||
      url.pathname.endsWith('.png') ||
      url.pathname.endsWith('.jpg') ||
      url.pathname.endsWith('.jpeg') ||
      url.pathname.endsWith('.webp') ||
      url.pathname.endsWith('.svg') ||
      url.pathname.endsWith('.woff2')
    ) {
      // Cache-First für statische Assets
      event.respondWith(cacheFirst(request));
    } else if (
      url.pathname.endsWith('.php') ||
      url.pathname.endsWith('/') ||
      url.pathname === '/'
    ) {
      // Network-First für HTML/PHP mit Offline-Fallback
      event.respondWith(networkFirst(request));
    }
  }
});

// Cache-First Strategie
async function cacheFirst(request) {
  const cache = await caches.open(CACHE_NAME);
  const cached = await cache.match(request);
  
  if (cached) {
    return cached;
  }

  try {
    const response = await fetch(request);
    if (response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  } catch (error) {
    console.error('Fetch failed:', error);
    throw error;
  }
}

// Network-First Strategie mit Offline-Fallback
async function networkFirst(request) {
  const cache = await caches.open(CACHE_NAME);
  
  try {
    const response = await fetch(request);
    if (response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  } catch (error) {
    const cached = await cache.match(request);
    if (cached) {
      return cached;
    }
    
    // Offline-Fallback für HTML-Seiten
    const offlinePage = await cache.match(OFFLINE_URL);
    if (offlinePage) {
      return offlinePage;
    }
    
    throw error;
  }
}
