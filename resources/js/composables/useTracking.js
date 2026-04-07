/**
 * Thin wrapper around GA4 / Meta / TikTok / Snapchat event APIs.
 *
 * Each method is a no-op when the corresponding pixel isn't loaded, so
 * callers don't need to guard on config availability. All events are
 * fanned out to every available provider with a sensible default mapping.
 *
 * Usage:
 *   import { useTracking } from '@/composables/useTracking';
 *   const track = useTracking();
 *   track.event('lead', { form: 'demo' });
 *   track.purchase({ value: 799, currency: 'EGP', transaction_id: 'INV-...' });
 */
export function useTracking() {
    const hasWindow = typeof window !== 'undefined';

    const ga4 = (name, params = {}) => {
        if (hasWindow && window.gtag) window.gtag('event', name, params);
    };

    const meta = (name, params = {}) => {
        if (hasWindow && window.fbq) window.fbq('track', name, params);
    };

    const tiktok = (name, params = {}) => {
        if (hasWindow && window.ttq) window.ttq.track(name, params);
    };

    const snap = (name, params = {}) => {
        if (hasWindow && window.snaptr) window.snaptr('track', name, params);
    };

    return {
        /** Raw pass-through. */
        event(name, params = {}) {
            ga4(name, params);
            meta(name, params);
            tiktok(name, params);
            snap(name, params);
        },

        /** User started the checkout flow (viewed the checkout page). */
        beginCheckout({ value, currency = 'EGP', items = [] } = {}) {
            ga4('begin_checkout', { value, currency, items });
            meta('InitiateCheckout', { value, currency, contents: items });
            tiktok('InitiateCheckout', { value, currency, contents: items });
            snap('START_CHECKOUT', { price: value, currency });
        },

        /** Successful purchase. Called from CheckoutSuccess page. */
        purchase({ value, currency = 'EGP', transaction_id, items = [] } = {}) {
            ga4('purchase', { value, currency, transaction_id, items });
            meta('Purchase', { value, currency, contents: items });
            tiktok('CompletePayment', { value, currency, contents: items });
            snap('PURCHASE', { price: value, currency, transaction_id });
        },

        /** Lead (demo request, contact, newsletter). */
        lead(extra = {}) {
            ga4('generate_lead', extra);
            meta('Lead', extra);
            tiktok('SubmitForm', extra);
            snap('SIGN_UP', extra);
        },

        /** User clicked a high-intent CTA (pricing, hero). */
        cta(label, extra = {}) {
            ga4('select_content', { content_type: 'cta', item_id: label, ...extra });
            meta('CustomEvent', { event: 'cta_click', label, ...extra });
        },

        /** Form submission (generic). */
        formSubmit(formName, extra = {}) {
            ga4('form_submit', { form_name: formName, ...extra });
            meta('SubmitApplication', { form: formName, ...extra });
            tiktok('SubmitForm', { form: formName, ...extra });
        },
    };
}
