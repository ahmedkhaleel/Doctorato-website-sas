<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sub-processors — Doctorato</title>
    <meta name="description" content="List of third-party services that may Process Personal Data on behalf of Doctorato customers, in accordance with GDPR Art. 28.">
    <link rel="canonical" href="https://doctorato.com/sub-processors">
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #FAF8F3; color: #1C2833;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.7; font-size: 16px; -webkit-font-smoothing: antialiased; }
        .wrap { max-width: 820px; margin: 0 auto; padding: 56px 24px 80px; }
        h1 { font-size: 30px; line-height: 1.25; color: #0A1628; margin: 0 0 6px; }
        h2 { font-size: 18px; color: #0A1628; margin: 36px 0 12px; }
        p { color: #5A6C7D; margin: 0 0 14px; }
        a { color: #0A1628; }
        a:hover { color: #C4A265; }
        .badge { display: inline-block; font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: #C4A265; font-weight: 700; margin-bottom: 14px; }
        .updated { color: #8B9BAC; font-size: 13px; margin: 0 0 36px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0 24px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(13,43,69,0.04); }
        th, td { padding: 14px 18px; text-align: start; border-bottom: 1px solid #EEE6D4; vertical-align: top; font-size: 14px; }
        th { background: #FAF8F3; color: #5A6C7D; font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; }
        tr:last-child td { border-bottom: none; }
        .label { color: #0A1628; font-weight: 600; }
        .region { display: inline-block; background: #FAF8F3; padding: 2px 8px; border-radius: 4px; font-size: 12px; color: #5A6C7D; }
        .notice { background: #FFF8E7; border: 1px solid #F5E0B0; border-radius: 12px; padding: 14px 18px; color: #6B5728; font-size: 14px; margin: 18px 0 24px; }
        footer { color: #8B9BAC; font-size: 12px; margin-top: 56px; padding-top: 28px; border-top: 1px solid #EEE6D4; }
    </style>
</head>
<body>
<div class="wrap">
    <span class="badge">Compliance</span>
    <h1>Sub-processors</h1>
    <p class="updated">Last updated 30 May 2026</p>

    <p>
        Below is the current list of third-party services that may Process
        Personal Data on behalf of Doctorato customers. The list is
        maintained in accordance with GDPR Art. 28(2) and our standard
        <a href="/responsible-disclosure">DPA</a>.
    </p>

    <div class="notice">
        <strong>Change notification:</strong> we publish updates to this
        list at least <strong>30 days</strong> before adding or
        replacing a sub-processor. Customers may object in writing
        within that window — see DPA §5.
    </div>

    <h2>Infrastructure</h2>
    <table>
        <thead>
            <tr><th>Sub-processor</th><th>Purpose</th><th>Region</th></tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Cloudflare, Inc.</td>
                <td>CDN, DNS, DDoS protection, TLS termination</td>
                <td><span class="region">Global edge</span></td>
            </tr>
            <tr>
                <td class="label">Egyptian shared host (cPanel)</td>
                <td>Application hosting, database (MySQL), file storage</td>
                <td><span class="region">Egypt</span></td>
            </tr>
            <tr>
                <td class="label">GitHub, Inc.</td>
                <td>Source code version control, CI/CD</td>
                <td><span class="region">USA</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Payments and billing</h2>
    <table>
        <thead>
            <tr><th>Sub-processor</th><th>Purpose</th><th>Region</th></tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Paymob Holding</td>
                <td>Payment processing, subscription billing, webhook delivery</td>
                <td><span class="region">Egypt / KSA / UAE</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Communications</h2>
    <table>
        <thead>
            <tr><th>Sub-processor</th><th>Purpose</th><th>Region</th></tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">cPanel SMTP relay</td>
                <td>Transactional email (magic links, invoices, drip)</td>
                <td><span class="region">Egypt</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Analytics and operations</h2>
    <table>
        <thead>
            <tr><th>Sub-processor</th><th>Purpose</th><th>Region</th></tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Google LLC — Analytics 4</td>
                <td>Anonymous traffic analytics (no PII)</td>
                <td><span class="region">USA / Global</span></td>
            </tr>
            <tr>
                <td class="label">Google LLC — reCAPTCHA</td>
                <td>Bot protection on public forms</td>
                <td><span class="region">USA / Global</span></td>
            </tr>
            <tr>
                <td class="label">Google LLC — Tag Manager</td>
                <td>Tag deployment (only when accepted via cookie banner)</td>
                <td><span class="region">USA / Global</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Geographic IP detection</h2>
    <table>
        <thead>
            <tr><th>Sub-processor</th><th>Purpose</th><th>Region</th></tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">country.is</td>
                <td>Country lookup for currency localisation</td>
                <td><span class="region">Global</span></td>
            </tr>
            <tr>
                <td class="label">ipapi.co</td>
                <td>Fallback country lookup</td>
                <td><span class="region">Global</span></td>
            </tr>
            <tr>
                <td class="label">freeipapi.com</td>
                <td>Second fallback country lookup</td>
                <td><span class="region">Global</span></td>
            </tr>
        </tbody>
    </table>
    <p>
        IP detection runs client-side or via Cloudflare headers; the
        services above are queried only as a fallback when neither is
        available. Only the visitor IP is sent — no other Personal Data.
    </p>

    <footer>
        Questions about this list: <a href="mailto:compliance@doctorato.com">compliance@doctorato.com</a>.
        Machine-readable security contact: <a href="/.well-known/security.txt"><code>/.well-known/security.txt</code></a>.
    </footer>
</div>
</body>
</html>
