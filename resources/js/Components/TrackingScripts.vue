<script setup>
/**
 * Injects analytics + marketing pixels into the page.
 *
 * Loaded once from MainLayout — no-ops when `tracking.enabled` is false or
 * a given pixel id is not configured. All scripts load asynchronously and
 * outside the critical path.
 *
 * Supported:
 *  - Google Analytics 4 (gtag.js)
 *  - Google Tag Manager (optional)
 *  - Meta (Facebook) Pixel
 *  - TikTok Pixel
 *  - Snapchat Pixel
 */
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, watch } from 'vue';

const page = usePage();

const tracking = computed(() => page.props.tracking || {});

function loadScript(src, attrs = {}) {
    if (typeof document === 'undefined') return;
    if (document.querySelector(`script[data-tid="${attrs['data-tid']}"]`)) return;
    const s = document.createElement('script');
    s.async = true;
    s.src = src;
    Object.entries(attrs).forEach(([k, v]) => s.setAttribute(k, v));
    document.head.appendChild(s);
}

function initGA4(id) {
    if (!id || window.gtag) return;
    loadScript(`https://www.googletagmanager.com/gtag/js?id=${id}`, { 'data-tid': `ga4-${id}` });
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', id, {
        anonymize_ip: true,
        send_page_view: true,
    });
}

function initGTM(id) {
    if (!id || window.google_tag_manager) return;
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
    loadScript(`https://www.googletagmanager.com/gtm.js?id=${id}`, { 'data-tid': `gtm-${id}` });
}

function initMetaPixel(id) {
    if (!id || window.fbq) return;
    // Meta Pixel boilerplate (minified intentionally per FB docs)
    /* eslint-disable */
    !(function (f, b, e, v, n, t, s) {
        if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
        if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
        t = b.createElement(e); t.async = !0; t.src = v; t.setAttribute('data-tid', `meta-${id}`);
        s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);
    })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
    /* eslint-enable */
    window.fbq('init', id);
    window.fbq('track', 'PageView');
}

function initTikTokPixel(id) {
    if (!id || window.ttq) return;
    /* eslint-disable */
    !(function (w, d, t) {
        w.TiktokAnalyticsObject = t; var ttq = w[t] = w[t] || [];
        ttq.methods = ['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'];
        ttq.setAndDefer = function (t, e) { t[e] = function () { t.push([e].concat(Array.prototype.slice.call(arguments, 0))); }; };
        for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
        ttq.instance = function (t) { for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]); return e; };
        ttq.load = function (e, n) {
            var r = 'https://analytics.tiktok.com/i18n/pixel/events.js';
            ttq._i = ttq._i || {}; ttq._i[e] = []; ttq._i[e]._u = r; ttq._t = ttq._t || {}; ttq._t[e] = +new Date(); ttq._o = ttq._o || {}; ttq._o[e] = n || {};
            var o = document.createElement('script'); o.type = 'text/javascript'; o.async = !0; o.src = r + '?sdkid=' + e + '&lib=' + t;
            o.setAttribute('data-tid', 'tiktok-' + e);
            var a = document.getElementsByTagName('script')[0]; a.parentNode.insertBefore(o, a);
        };
        ttq.load(id);
        ttq.page();
    })(window, document, 'ttq');
    /* eslint-enable */
}

function initSnapPixel(id) {
    if (!id || window.snaptr) return;
    /* eslint-disable */
    (function (e, t, n) {
        if (e.snaptr) return;
        var a = e.snaptr = function () { a.handleRequest ? a.handleRequest.apply(a, arguments) : a.queue.push(arguments); };
        a.queue = [];
        var s = 'script'; var r = t.createElement(s); r.async = !0; r.src = n; r.setAttribute('data-tid', `snap-${id}`);
        var u = t.getElementsByTagName(s)[0]; u.parentNode.insertBefore(r, u);
    })(window, document, 'https://sc-static.net/scevent.min.js');
    /* eslint-enable */
    window.snaptr('init', id);
    window.snaptr('track', 'PAGE_VIEW');
}

function initAll() {
    if (!tracking.value.enabled) return;
    initGA4(tracking.value.ga4_id);
    initGTM(tracking.value.gtm_id);
    initMetaPixel(tracking.value.meta_pixel_id);
    initTikTokPixel(tracking.value.tiktok_pixel_id);
    initSnapPixel(tracking.value.snapchat_pixel_id);
}

onMounted(initAll);

// Track SPA navigation as page_view for GA4 / Meta / TikTok
watch(
    () => page.url,
    (newUrl) => {
        if (!tracking.value.enabled) return;
        if (window.gtag && tracking.value.ga4_id) {
            window.gtag('event', 'page_view', {
                page_path: newUrl,
                page_location: window.location.href,
                page_title: document.title,
            });
        }
        if (window.fbq) window.fbq('track', 'PageView');
        if (window.ttq) window.ttq.page();
        if (window.snaptr) window.snaptr('track', 'PAGE_VIEW');
    }
);
</script>

<template>
    <!-- Pixel/analytics loader — no visible output -->
    <noscript v-if="tracking.enabled && tracking.meta_pixel_id">
        <img
            height="1"
            width="1"
            style="display:none"
            :src="`https://www.facebook.com/tr?id=${tracking.meta_pixel_id}&ev=PageView&noscript=1`"
            alt=""
        />
    </noscript>
</template>
