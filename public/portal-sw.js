/**
 * Doctorato Portal — minimal service worker.
 *
 * Design goals:
 *   1. Make the portal installable as a PWA.
 *   2. Show a graceful offline page instead of the browser default
 *      when the customer opens the installed app without a network.
 *   3. NEVER cache portal data (subscriptions, invoices, profile)
 *      — that data is sensitive and can go stale within minutes.
 *
 * What we DO cache: the static shell (offline page + logo + manifest)
 * so the installed app can paint something useful before falling
 * back to live data.
 *
 * What we DO NOT cache:
 *   - GET requests under /portal/dashboard, /portal/refer, etc.
 *     These are passthrough to the network; a failure shows the
 *     offline page.
 *   - POST/PUT/DELETE: never serviced by the SW. They must hit the
 *     network so the server validates the session + CSRF token.
 *   - Anything under /admin/* — different audience, different
 *     security posture, not in our scope='/portal/'.
 *
 * Versioning: change PORTAL_SW_VERSION to force a fresh install on
 * existing clients. Old caches are deleted in the activate handler.
 */

const PORTAL_SW_VERSION = 'v1';
const SHELL_CACHE = `doctorato-portal-shell-${PORTAL_SW_VERSION}`;

const SHELL_ASSETS = [
    '/portal/offline',
    '/images/doctorato-logo.png',
    '/manifest.webmanifest',
];

self.addEventListener('install', (event) => {
    // Pre-cache the offline shell so first offline open works.
    event.waitUntil(
        caches.open(SHELL_CACHE)
            .then((cache) => cache.addAll(SHELL_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    // Drop any caches that don't match the current version.
    event.waitUntil(
        caches.keys().then((names) =>
            Promise.all(names
                .filter((n) => n.startsWith('doctorato-portal-shell-') && n !== SHELL_CACHE)
                .map((n) => caches.delete(n))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;

    // Only intercept GETs. POSTs etc go straight to the network.
    if (req.method !== 'GET') return;

    const url = new URL(req.url);

    // Same-origin only. Cross-origin requests (CDN, analytics)
    // bypass the SW entirely.
    if (url.origin !== self.location.origin) return;

    // Out of scope: anything outside /portal/ + the cached shell
    // assets. Bypass cleanly so the rest of the site is unaffected.
    const isShellAsset = SHELL_ASSETS.includes(url.pathname);
    const isPortalPath = url.pathname.startsWith('/portal/');
    if (!isShellAsset && !isPortalPath) return;

    // Network-first for portal pages — they hold live customer data.
    // Cache-only fallback to the offline shell page when offline.
    event.respondWith((async () => {
        try {
            return await fetch(req);
        } catch (e) {
            if (isShellAsset) {
                const cached = await caches.match(req);
                if (cached) return cached;
            }
            // Live page failed → serve the offline shell so the
            // customer sees branded content, not the browser's
            // Chrome dino.
            const offline = await caches.match('/portal/offline');
            if (offline) return offline;
            // Final fallback: re-throw so the browser handles it.
            throw e;
        }
    })());
});
