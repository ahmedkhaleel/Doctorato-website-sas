# Doctorato — Marketing Website + Customer Portal

Bilingual (Arabic / English) marketing website and self-service customer
portal for **Doctorato**, a clinic management SaaS for the Middle East.

Built on **Laravel 13** + **Inertia.js** + **Vue 3** + **Tailwind CSS**.
Deployed on cPanel shared hosting (ea-php84).

---

## What lives here

- **Public marketing site** — landing, features, pricing, country
  pages, blog, case studies, demo + contact forms.
- **Admin panel** (`/admin`) — content management (blog, FAQs, plans,
  testimonials, case studies), users + permissions, activity log,
  webhook inspector, GDPR/PDPL data-subject console, settings.
- **Customer portal** (`/portal`) — magic-link auth, subscription
  management, invoices, profile + preferences, referral program,
  trial status tracking.
- **Public status page** (`/status`) — health-endpoint-backed
  uptime view.

---

## Quick start

```bash
# 1. Install
composer install
npm ci

# 2. Configure
cp .env.example .env
php artisan key:generate

# Production sets DB_CONNECTION=mysql + the four DB_* vars.
# Local can stay on sqlite (default).

# 3. Migrate + seed
php artisan migrate
php artisan db:seed

# 4. Build assets
npm run build

# 5. Serve
php artisan serve
```

Production deploys run through `public/deploy.php` (HMAC-protected
GitHub webhook). See `DISASTER_RECOVERY.md` for the cron entry that
keeps the scheduler ticking.

---

## Production constraints

These shape almost every design decision in this codebase. Read them
before adding new code:

| Constraint | Implication |
|---|---|
| **No PHP `curl` extension on prod** | Outbound HTTPS from PHP isn't reliable. Use `canMakeOutboundRequests()` guards + browser-side fallbacks. |
| **No `allow_url_fopen`** | Same. The geo-detection chain has three providers + a JS fallback for this reason. |
| **Email is the magic-link identifier** | Customer email must NEVER be editable through portal profile update — would lock them out. |
| **Marketing opt-out timestamps persist forever** | Compliance audit trail. Don't clear them on re-opt-in. |
| **Webhook idempotency required** | Paymob retries on non-200. The DB transaction + `lockForUpdate` guard in `PaymobWebhookController` is load-bearing. |
| **All migrations idempotent** | Use `Schema::hasColumn` guards. Re-runs MUST be safe. |
| **Subscription cancel is SOFT** | Stays active until `ends_at`. No mid-cycle access loss. |
| **Single cron entry** | Runs `php artisan schedule:run`. All periodic tasks declared in `routes/console.php`. |
| **No new composer deps** unless absolutely necessary | Host has had recurring extension/dep resolution issues. |
| **Egyptian 5-year tax retention** | Financial records (invoices, payments, subscriptions) preserved on GDPR erase — PII overwritten, rows kept. |

---

## Testing

```bash
# Run the green suite (Phase 10-15)
php artisan test --filter="SecurityHeadersTest|ReferralProgramTest|TrialDripTest|RetentionAndLockoutTest|GdprAndScrubberTest|RateLimiterMatrixTest|WebhookEventStoreTest"
```

The legacy factory-based tests (`CustomerPortalTest`, `DemoRequestTest`,
etc.) currently fail on a pre-existing factory wiring issue and are
excluded from the CI green suite. Don't gate new work on them.

CI runs both PHP 8.3 and 8.4 against the same green suite plus a
`php -l` lint pass on every PHP file. See `.github/workflows/ci.yml`.

---

## Operational documentation

- **[CHANGELOG.md](./CHANGELOG.md)** — every shipped feature, Phase 1-17.
- **[OPERATIONS.md](./OPERATIONS.md)** — 13-section operator handbook
  (deployment, backups, monitoring, common-failure playbook, DB
  hardening, email migration plan).
- **[DISASTER_RECOVERY.md](./DISASTER_RECOVERY.md)** — runbook for host
  outage, DB loss, payment failures, credential rotation. RTO 4h / RPO
  24h.

---

## Architectural cheat-sheet

```
/                                  Public Inertia/Vue pages
├── /demo, /contact, /pricing      Lead capture
├── /blog, /{country}              Content + SEO
├── /portal/*                      Magic-link customer area
└── /admin/*                       Role-gated admin panel

app/
├── Http/Controllers/
│   ├── Admin/                     Admin controllers (Form-Request-gated)
│   ├── CustomerPortalController   Portal endpoints
│   └── PaymobWebhookController    Payment provider webhook
├── Http/Requests/Admin/           Permission-gated Form Requests
├── Http/Middleware/
│   ├── CustomerAuth               Portal session gate
│   ├── SecurityHeaders            HSTS / CSP / Permissions-Policy
│   └── CaptureReferralCode        ?ref= cookie capture
├── Services/
│   ├── PaymobService              HMAC + checkout
│   ├── ReferralService            Two-sided credit logic
│   ├── GdprService                Export + erase
│   ├── LoginAttemptTracker        Sliding-window lockout
│   ├── CountryDetector            3-provider chain + JS fallback
│   └── ...
├── Logging/
│   ├── PiiScrubbingProcessor      Email/phone/card redaction
│   └── AddPiiScrubber             Monolog tap
└── Console/Commands/
    ├── BackupDatabase             mysqldump + PHP fallback
    ├── RunDunning                 day-0/3/7/10/30 state machine
    ├── PruneRetention             365d/30d/7d table trim
    ├── SendTrialDrip              3-step welcome drip
    └── ...

routes/
├── web.php                        All HTTP routes
└── console.php                    Cron-driven tasks (jittered)
```

---

## Contributing

1. Branch off `main`. Conventional commits encouraged.
2. Add tests under `tests/Feature/` matching the existing naming
   pattern (`PhaseFeatureTest.php`).
3. Run `php artisan test --filter=<YourTest>` locally before pushing.
4. CI will run the full green suite + lint on PR.
5. Read the relevant production constraint above before adding code
   that talks to the network or modifies a customer-facing field.

---

## License

Proprietary. © Doctorato. All rights reserved.
