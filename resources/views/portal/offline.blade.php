<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Offline — Doctorato</title>
    <style>
        /* Self-contained — this page must render with zero network. */
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #F4F1EA;
            color: #1C2833;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .card {
            max-width: 480px;
            width: 100%;
            background: #fff;
            border-radius: 16px;
            padding: 36px 28px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(13, 43, 69, 0.04), 0 12px 32px rgba(13, 43, 69, 0.06);
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 24px;
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0A1628;
            margin: 0 0 8px;
            letter-spacing: -0.01em;
        }
        p {
            font-size: 14px;
            color: #5A6C7D;
            line-height: 1.6;
            margin: 0 0 24px;
        }
        .retry-btn {
            display: inline-block;
            background: #0A1628;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background 150ms ease;
        }
        .retry-btn:hover { background: #1C2833; }
        .badge {
            display: inline-block;
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #C4A265;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .status {
            margin-top: 20px;
            font-size: 12px;
            color: #8B9BAC;
        }
        .status .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #C4A265;
            margin-inline-end: 6px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="card">
        <img class="logo" src="/images/doctorato-logo.png" alt="Doctorato">
        <span class="badge">Offline</span>
        <h1>You're offline right now.</h1>
        <p>
            Doctorato Portal needs an internet connection to load your subscriptions and invoices.
            We'll be right here the moment you're back online.
        </p>
        <button class="retry-btn" onclick="location.reload()">Try again</button>
        <p class="status">
            <span class="dot"></span>
            <span id="status-label">Waiting for a connection…</span>
        </p>
    </div>

    <script>
        // Auto-retry when the browser regains connectivity. The
        // 'online' event fires reliably on iOS Safari, Android
        // Chrome, and desktop browsers — no polling needed.
        window.addEventListener('online', () => {
            document.getElementById('status-label').textContent = 'Back online — reloading…';
            setTimeout(() => location.reload(), 600);
        });
        // If already online when the offline page loads (rare —
        // usually a stale SW cache), reload immediately.
        if (navigator.onLine) {
            setTimeout(() => location.reload(), 1200);
        }
    </script>
</body>
</html>
