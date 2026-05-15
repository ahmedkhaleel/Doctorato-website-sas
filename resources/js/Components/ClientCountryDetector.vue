<script setup>
/**
 * Browser-side country detection — the safety net for shared hosting
 * environments where the PHP curl extension and allow_url_fopen are
 * both disabled, so the server can't reach any geo API directly.
 *
 * Flow:
 *   1. Server resolves country. If every server-side signal fails
 *      (no Cloudflare, no Apache header, no PHP geoip, no curl),
 *      Laravel sets `geoNeedsClient = true` in the shared Inertia
 *      props and falls back to 'EG' for the first paint.
 *   2. This component, mounted globally in MainLayout, sees the flag
 *      on mount, calls a free HTTPS geo API from the browser (no
 *      CORS issues, no PHP involved), and POSTs the country back to
 *      Laravel via /_set-detected-country.
 *   3. Laravel stores the code in the session as source='browser'
 *      so future requests in the same session use it directly.
 *   4. router.reload() refetches the page with the right country
 *      prop — pricing flips to AED, flag flips to 🇦🇪, WhatsApp
 *      flips to the local number.
 *
 * sessionStorage flag prevents repeat detection on every page nav.
 * Falls silently on failure — user just sees the EG fallback.
 */
import { onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();

// Free HTTPS geo APIs (no key, no CORS issues). Tried in order;
// first one that returns a valid 2-letter code wins.
const providers = [
    {
        name: 'country.is',
        fetch: async () => {
            const res = await fetch('https://api.country.is/', { mode: 'cors' });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            return data.country;
        },
    },
    {
        name: 'ipapi.co',
        fetch: async () => {
            const res = await fetch('https://ipapi.co/country/', { mode: 'cors' });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return (await res.text()).trim();
        },
    },
    {
        name: 'freeipapi.com',
        fetch: async () => {
            const res = await fetch('https://freeipapi.com/api/json/', { mode: 'cors' });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            return data.countryCode;
        },
    },
];

async function detectFromBrowser() {
    for (const provider of providers) {
        try {
            const code = await provider.fetch();
            if (code && /^[A-Z]{2}$/i.test(code)) {
                return code.toUpperCase();
            }
        } catch (e) {
            // Try next provider
            console.debug(`[geo] ${provider.name} failed:`, e.message);
        }
    }
    return null;
}

async function reportToServer(code) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]
        || '';
    try {
        await fetch('/_set-detected-country', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(csrfToken),
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ country: code }),
        });
    } catch (e) {
        console.debug('[geo] report failed:', e.message);
    }
}

onMounted(async () => {
    // Server-side detection worked — nothing to do.
    if (!page.props.geoNeedsClient) return;

    // Already detected in this browser session — don't ping the
    // geo API on every page navigation.
    if (sessionStorage.getItem('country-detected')) return;
    sessionStorage.setItem('country-detected', '1');

    const code = await detectFromBrowser();
    if (!code) return;

    // Same as the current value — no need to round-trip.
    if (code === page.props.activeCountry) return;

    await reportToServer(code);

    // Refresh the country-dependent shared props so flags, prices,
    // and the WhatsApp number all flip without a full page reload.
    router.reload({ only: ['activeCountry', 'site'], preserveScroll: true });
});
</script>

<template>
    <!-- Headless component — no UI. Lives in MainLayout. -->
</template>
