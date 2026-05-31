<?php

/**
 * Notification routing — where admin notifications get delivered.
 *
 * These are the addresses that receive a new-record email every time a
 * visitor submits the corresponding public form. Customer confirmations
 * still go to the visitor's own address; these values control the
 * INTERNAL copy that lands in your team's inbox.
 *
 * Override per-environment via .env:
 *   NOTIFY_DEMO_EMAIL=demo@doctorato.com
 *   NOTIFY_CONTACT_EMAIL=info@doctorato.com
 *
 * Sensible defaults are wired in below so the app keeps working even
 * when .env hasn't been touched yet.
 */
return [
    // Demo / trial requests (Demo.vue → POST /demo-request)
    'demo_email' => env('NOTIFY_DEMO_EMAIL', 'demo@doctorato.com'),

    // Contact form submissions (Contact.vue → POST /contact)
    'contact_email' => env('NOTIFY_CONTACT_EMAIL', 'info@doctorato.com'),
];
