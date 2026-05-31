<?php

/**
 * Notification routing — who receives each public form submission.
 *
 * Sender: a single FROM address configured in .env via MAIL_FROM_ADDRESS
 * (set to info@doctorato.com on production). Every notification — Demo,
 * Contact, future forms — leaves from the same mailbox so admin replies
 * thread cleanly and SPF/DKIM stay aligned with one identity.
 *
 * Recipients: both info@ AND demo@ receive a copy of every form so that
 * either inbox can be the working one without missing leads.
 *
 * Override per-environment via .env (comma-separated list, no spaces):
 *   NOTIFY_DEMO_RECIPIENTS=info@doctorato.com,demo@doctorato.com
 *   NOTIFY_CONTACT_RECIPIENTS=info@doctorato.com,demo@doctorato.com
 *
 * The controller code below converts these into Mail::to([...]) arrays so
 * each address gets its own message (visible TO field, not BCC) — easier
 * to filter and reply from either inbox.
 */
return [
    // Demo / trial requests (Demo.vue → POST /demo-request).
    // Defaults: both info@ and demo@ receive every submission.
    'demo_recipients' => array_values(array_filter(array_map('trim', explode(',',
        env('NOTIFY_DEMO_RECIPIENTS', 'info@doctorato.com,demo@doctorato.com')
    )))),

    // Contact form submissions (Contact.vue → POST /contact).
    // Defaults: same dual delivery.
    'contact_recipients' => array_values(array_filter(array_map('trim', explode(',',
        env('NOTIFY_CONTACT_RECIPIENTS', 'info@doctorato.com,demo@doctorato.com')
    )))),

    // Legacy single-address keys — kept for backward compatibility with
    // any external scripts that read them. Resolve to the first recipient.
    'demo_email' => env('NOTIFY_DEMO_EMAIL', 'info@doctorato.com'),
    'contact_email' => env('NOTIFY_CONTACT_EMAIL', 'info@doctorato.com'),
];
