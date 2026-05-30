<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Responsible Disclosure — Doctorato</title>
    <meta name="description" content="Security vulnerability disclosure policy and safe-harbour terms for Doctorato.">
    <link rel="canonical" href="https://doctorato.com/responsible-disclosure">
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #FAF8F3;
            color: #1C2833;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.7;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
        }
        .wrap { max-width: 720px; margin: 0 auto; padding: 56px 24px 80px; }
        h1 { font-size: 30px; line-height: 1.25; letter-spacing: -0.01em; color: #0A1628; margin: 0 0 6px; }
        h2 { font-size: 18px; color: #0A1628; margin: 36px 0 12px; letter-spacing: -0.005em; }
        h3 { font-size: 15px; color: #0A1628; margin: 24px 0 8px; font-weight: 700; }
        p { color: #5A6C7D; margin: 0 0 14px; }
        ul, ol { color: #5A6C7D; padding-inline-start: 20px; margin: 0 0 14px; }
        li { margin-bottom: 6px; }
        code { background: #FFF; border: 1px solid #EEE6D4; padding: 1px 6px; border-radius: 4px; font-size: 13px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
        a { color: #0A1628; text-decoration: underline; }
        a:hover { color: #C4A265; }
        .badge { display: inline-block; font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: #C4A265; font-weight: 700; margin-bottom: 14px; }
        .updated { color: #8B9BAC; font-size: 13px; margin: 0 0 36px; }
        .pill { display: inline-block; background: #0A1628; color: #FFF; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; margin: 8px 0; }
        .pill:hover { background: #1C2833; color: #FFF; }
        footer { color: #8B9BAC; font-size: 12px; margin-top: 56px; padding-top: 28px; border-top: 1px solid #EEE6D4; }
        hr { border: none; border-top: 1px solid #EEE6D4; margin: 32px 0; }
    </style>
</head>
<body>
<div class="wrap">
    <span class="badge">Security</span>
    <h1>Responsible Disclosure</h1>
    <p class="updated">Last updated 30 May 2026</p>

    <p>
        If you've found a security vulnerability in Doctorato, please tell us
        before you tell anyone else. This page explains how to report,
        what we'll do in response, and the safe-harbour terms we offer
        to security researchers acting in good faith.
    </p>

    <p><strong>No bug bounty.</strong> We're a small team and don't pay
        monetary rewards. We offer public acknowledgement and a written
        thank-you for verified reports.</p>

    <h2>Report a vulnerability</h2>
    <a href="mailto:security@doctorato.com?subject=Security%20report" class="pill">security@doctorato.com</a>
    <p>Please include:</p>
    <ol>
        <li>Description and impact.</li>
        <li>Reproduction steps detailed enough to verify.</li>
        <li>Optional proof-of-concept screenshots or code (no real customer data — use synthetic accounts).</li>
        <li>Your name or handle for acknowledgement (optional).</li>
    </ol>

    <p>Our response targets:</p>
    <ul>
        <li>Acknowledgement within <strong>48 business hours</strong></li>
        <li>Triage decision within <strong>5 business days</strong></li>
        <li>Fix or mitigation timeline within <strong>7 days</strong> for High/Critical, <strong>30 days</strong> for Medium</li>
    </ul>

    <h2>Safe-harbour scope</h2>
    <p>We will not pursue legal action against researchers who:</p>
    <ul>
        <li>Test only against accounts and data they own (or have explicit permission to test).</li>
        <li>Use the minimum data needed to demonstrate the issue.</li>
        <li>Avoid degrading service for other users (no DoS / load testing without prior approval).</li>
        <li>Don't pivot from one finding to another customer's data.</li>
        <li>Give us a reasonable disclosure window — default <strong>90 days</strong>.</li>
    </ul>
    <p>Safe harbour covers <code>doctorato.com</code> and any <code>*.doctorato.com</code> subdomain.</p>

    <h2>Out of scope</h2>
    <ul>
        <li>Self-XSS that only affects the attacker's own session.</li>
        <li>SPF / DKIM / DMARC weaknesses without real-world abuse.</li>
        <li>Outdated TLS cipher suites on the Cloudflare front-door.</li>
        <li>Missing headers on CDN-served static assets.</li>
        <li>Social-engineering against staff or customers.</li>
        <li>Issues requiring physical access to a victim's unlocked device.</li>
        <li>Third-party services we integrate with (please report upstream).</li>
        <li>Rate-limit findings on endpoints already documented with a throttle: middleware.</li>
    </ul>

    <h2>What we ask of you</h2>
    <ul>
        <li>Don't publicly disclose before the fix ships (or before 90 days elapse).</li>
        <li>Don't access, modify, or delete customer data beyond demonstration.</li>
        <li>Don't run automated scanners against <code>/admin/*</code> or <code>/portal/*</code> without contacting us first — those surfaces are auth-gated and scanner noise drowns real reports.</li>
        <li>Don't demand monetary reward as a condition of reporting.</li>
    </ul>

    <hr>

    <h2>Acknowledgements</h2>
    <p><em>No public reports yet. Be the first.</em></p>

    <footer>
        Machine-readable contact: <a href="/.well-known/security.txt"><code>/.well-known/security.txt</code></a>
        · RFC 9116
    </footer>
</div>
</body>
</html>
