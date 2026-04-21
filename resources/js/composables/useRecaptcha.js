/**
 * Google reCAPTCHA v3 composable.
 *
 * Lazy-loads the Google script only the first time a form actually
 * needs it, so pages without forms don't pay the network cost. When
 * reCAPTCHA isn't configured (keys missing), `execute` quietly returns
 * null and the form submits anyway — the backend still has the
 * honeypot + timing checks as always-on defenses.
 *
 * Usage:
 *   const { ensureReady, execute } = useRecaptcha();
 *   // Somewhere on mount: ensureReady();
 *   // On submit:           const token = await execute('demo_form');
 */
import { usePage } from '@inertiajs/vue3';

let loadPromise = null;

function loadScript(siteKey) {
    if (loadPromise) return loadPromise;
    loadPromise = new Promise((resolve, reject) => {
        if (typeof window === 'undefined') return reject(new Error('no window'));
        if (window.grecaptcha && window.grecaptcha.ready) return resolve();

        const s = document.createElement('script');
        s.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
        s.async = true;
        s.defer = true;
        s.onload = () => resolve();
        s.onerror = () => reject(new Error('recaptcha script failed to load'));
        document.head.appendChild(s);
    });
    return loadPromise;
}

export function useRecaptcha() {
    const page = usePage();

    const config = () => page.props.recaptcha || {};
    const enabled = () => !!config().enabled;
    const siteKey = () => config().site_key;

    async function ensureReady() {
        if (!enabled()) return false;
        try {
            await loadScript(siteKey());
            return true;
        } catch (_) {
            return false;
        }
    }

    /**
     * Execute reCAPTCHA for the given action. Returns the token, or
     * null if reCAPTCHA isn't configured / failed to load.
     */
    async function execute(action = 'submit') {
        if (!enabled()) return null;
        const ok = await ensureReady();
        if (!ok) return null;
        return new Promise((resolve) => {
            try {
                window.grecaptcha.ready(() => {
                    window.grecaptcha
                        .execute(siteKey(), { action })
                        .then((token) => resolve(token))
                        .catch(() => resolve(null));
                });
            } catch (_) {
                resolve(null);
            }
        });
    }

    /** The timestamp (ms) the form was rendered — used by the backend
     *  to reject submissions that were posted in under 1.5 seconds. */
    function rendered_at() {
        return Date.now();
    }

    return { enabled, siteKey, ensureReady, execute, rendered_at };
}
