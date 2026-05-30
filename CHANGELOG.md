# Changelog

All notable changes to Doctorato. Follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This file documents Phase 1–16 of the operational-hardening roadmap.
Earlier feature work (SEO infra, blog, country pages, hreflang, RSS,
KG, geo-detection, contact forms, email system, footer redesign) is
captured in commit history rather than re-summarised here.

---

## [Unreleased]

### Added
- **Phase 16 — CI/CD + cron jitter + changelog**
  - GitHub Actions workflow (`.github/workflows/ci.yml`) runs PHP
    8.3 + 8.4 matrix, mirrors prod extension set (no curl), executes
    the green test suite, and lints every PHP file with `php -l`.
  - Cron tasks jittered off `:00` / `:30` (`03:00`, `03:37`, `09:07`,
    `10:13`, `11 * * * *`) to avoid host-wide thundering herd on
    shared cPanel.
  - Inline documentation in `routes/console.php` explaining the
    jitter policy for future task additions.
  - This `CHANGELOG.md` summarising Phase 1–16.

- **Phase 15 — Named rate-limiter matrix + DR runbook**
  - Eight named rate limiters in `AppServiceProvider`: `demo-submit`,
    `contact-submit`, `newsletter-submit`, `portal-login`,
    `admin-login`, `two-factor`, `webhooks`, `healthchecks`.
    Composite keying (`email + IP`) where the surface targets a
    specific identity; per-IP otherwise.
  - `/portal/login` now caps per (email, IP) AND per email to
    prevent email-bombing without locking out NATed offices.
  - All public POST routes migrated from numeric throttle to named.
  - `DISASTER_RECOVERY.md` operator runbook: decision tree, restore
    from `db:backup`, credential rotation with forensics, monthly
    backup verification drill. RTO 4h / 24h, RPO 24h.

- **Phase 14 — GDPR / PDPL data-subject rights + PII log scrubbing**
  - `PiiScrubbingProcessor` (Monolog processor) redacts email,
    phone (+ country form only), credit cards, and sensitive keys
    (`password`, `secret`, `token`, `recaptcha_token`, etc) BEFORE
    log records hit disk. Bolted onto `single` + `daily` channels
    via `AddPiiScrubber` tap class. ISO timestamps preserved.
  - `GdprService::export(email)` collates demo + subscriptions +
    invoices + payments + audit mentions into a structured JSON
    that the admin controller streams as a download.
  - `GdprService::erase(email, reason)` runs in a DB transaction:
    PII overwritten with `[ERASED]` sentinels (rows preserved so
    FK references to financial records stay intact for Egyptian
    5y tax retention), `login_attempts` for the hashed email
    dropped, activity-log descriptions scrubbed with the same
    regex set, and a `gdpr_delete` tombstone row inserted.
  - `/admin/gdpr` Vue console gated on new `gdpr.manage`
    permission (DPO/owner only). Erase requires typing `ERASE` to
    confirm.

- **Phase 13 — Retention pruner + login-attempt lockout + session hardening**
  - `php artisan maint:prune` + daily scheduler at `03:37`:
    `activity_logs > 365d` (except `PERMANENT_ACTIONS`),
    `failed_jobs > 30d`, `sessions > 30d past last_activity`,
    `customer_login_tokens > 7d`. Idempotent + `--dry`.
  - `login_attempts` table + `LoginAttemptTracker` service. Email
    stored as SHA-256 so the table itself doesn't leak the admin
    roster if dumped. Sliding-window policy: 5 fails per email /
    15 min → 30 min lock; 20 fails per IP / 15 min → 60 min lock.
    `AuthController` checks `lockedUntil()` BEFORE password
    validation to deny timing oracle.
  - `.env.example` session hardening: `SESSION_ENCRYPT=true`,
    `SESSION_SECURE_COOKIE=true`, `SESSION_SAME_SITE=lax`,
    `SESSION_HTTP_ONLY=true`.

- **Phase 12 — Security headers + form-request batch**
  - `SecurityHeaders` middleware on web group: HSTS (1y), X-Frame
    SAMEORIGIN, X-Content nosniff, Referrer-Policy strict-origin,
    Permissions-Policy denying camera/mic/geo/payment/usb,
    Content-Security-Policy scoped to actual script sources.
    Webhook + `/healthz` exempt.
  - Form Requests for the two highest-risk admin controllers
    (`StoreUserRequest`, `UpdateUserRequest`, `PlanRequest`).
  - **Phase 12b**: 12 more admin controllers converted to Form
    Requests (`AddOn`, `BlogCategory`, `BlogPost`, `CaseStudy`,
    `Coupon`, `Currency`, `UpdateDemoStatus`, `EmailTemplate`,
    `Faq`, `PlanPrice`, three `Settings*`, `Testimonial`). Each
    gated on a per-area permission key, removing ~250 lines of
    inline `$request->validate()` from controllers.
  - `.env.example` hygiene: `MAIL_MAILER=smtp` default,
    `DB_USERNAME=doctorato_app` with minimum-grant SQL comment.

- **Phase 11 — Two-sided referral program**
  - `?ref=DOC-XXXXXXXX` captured into a 30-day first-touch cookie
    by `CaptureReferralCode` middleware (regex-validated format,
    not overwritten on subsequent visits).
  - `subscriptions.referral_code` (unique, generated on
    activation), `referred_by_subscription_id`,
    `referral_credit_cents`. Demo form posts the cookie value
    through to `demo_requests.referred_by_code`.
  - `ReferralService::onSubscriptionActivated()` called from the
    Paymob webhook inside the same transaction — credits the
    referrer 10% of first payment (capped at one month). Self-
    referral blocked, unknown codes silently dropped, double-
    activation idempotent.
  - `/portal/refer` page: empty state, credit balance, copy-link
    with Clipboard API + execCommand fallback, WhatsApp + email
    share helpers, list of referred clinics.

- **Phase 10 — Trial welcome drip + portal status panel**
  - `php artisan trials:drip` + daily scheduler at `10:13`. Three
    steps: welcome (day 0), tour (day 3), case study (day 7).
    Processes steps in **reverse** order so a single run advances
    each trial at most one step (no email-flood if cron was
    paused).
  - Single `TrialDripMail` powers all three steps via a STEPS
    constant. Shared blade shell for consistent brand chrome.
  - Skips opted-out, expired, and cancelled trials. Stamps
    `trial_drip_step` + `trial_drip_last_sent_at` idempotently.
  - `/portal/dashboard` gains a Trial Status card with progress
    bar + 3-step onboarding checklist driven by `trial_drip_step`.

- **Phase 9 — Customer portal profile + preferences**
  - `/portal/profile` edit form (email read-only — it's the
    magic-link identifier).
  - Marketing-preference toggle with persistent `marketing_
    opted_out_at` timestamp (kept on re-opt-in for audit trail).
  - `resumeSubscription()` lets a customer reactivate a canceled
    sub during the grace window.
  - `OPERATIONS.md` 13-section operator handbook.

- **Phase 8 — Print-ready HTML invoice**
  - `/portal/invoices/{id}` with `@media print` stylesheet. Browser
    handles PDF export via Cmd+P — avoids bundling a PHP PDF
    library (mpdf/dompdf transitive deps were prohibitive on
    cPanel).
  - Strict ownership guard.

- **Phase 7 — Activity-log UI polish + exception notifier**
  - `AdminExceptionNotifier` Sentry-lite: emails admin on
    exceptions with sha1 signature throttling (1 mail/hour per
    signature).
  - Activity-log Vue page with expandable diff rows + CSV export.

- **Phase 5+6 — Two-factor auth + customer magic-link portal + status page**
  - `TwoFactorService` pure-PHP RFC 6238 TOTP (~60 lines, no
    external library — cPanel has no Composer Twilio etc).
  - `CustomerLoginToken` SHA-256 hashed, single-use, 15 min TTL.
  - `CustomerAuth` middleware separates portal session from
    admin session.
  - `/portal/login`, `/portal/dashboard` Vue pages.
  - `/status` public status page reading from health endpoints.

- **Phase 4 — TOTP 2FA + is_active + permission whitelist**
  - 3-stage admin login: credentials → is_active → 2FA challenge.
  - `users.is_active` column.

- **Phase 3 — Backups + dunning + health endpoints + scheduler**
  - `BackupDatabase` artisan command with `mysqldump` primary +
    pure-PHP fallback.
  - `RunDunning` state machine over `invoices.dunning_stage`
    (day 0, 3, 7, 10, 30).
  - `/healthz` shallow + `/healthz/deep`.
  - Single cron entry runs `schedule:run`; all tasks declared in
    `routes/console.php`.

- **Phase 2 — Rate limits + cache + form requests + activity log**
  - `StoreContactRequest` + `StoreDemoRequest`.
  - `PublicContentCache` TTL 600s, observer-invalidated.
  - `LogsActivity` trait auto-logs create/update/delete with
    field diffs.
  - `QueueHealthCheck` artisan command.
  - Cache driver flipped from `database` to `file` (10× speedup).

- **Phase 1 — Queue + indexes + webhook idempotency**
  - All 4 mailables `implements ShouldQueue` with `tries=3`,
    `backoff=60`.
  - 20+ composite indexes on hot paths.
  - `PaymobWebhookController` idempotency guard + `lockForUpdate`
    so Paymob retries can't double-bill.

### Production constraints carried forward through all phases

These are baked into design choices and must remain true:

- **No PHP curl extension and no `allow_url_fopen`** on the
  cPanel host. All integrations must work without outbound HTTPS
  from PHP (browser-side geo detection,
  `canMakeOutboundRequests()` guards, `AdminExceptionNotifier`
  via SMTP instead of Sentry).
- **Email is the magic-link identifier** for customers — must
  NEVER be editable via the portal profile update.
- **Marketing opt-out timestamps persist forever** for compliance
  audit; must NOT be cleared on opt-in.
- **Webhook idempotency** is required to prevent double-billing
  on Paymob retries.
- **All migrations are idempotent** (`Schema::hasColumn` checks).
- **Subscription cancel is SOFT** (stays active until `ends_at`).
- **The single cron entry** runs `php artisan schedule:run`. All
  periodic tasks declared in `routes/console.php`.
- **No new composer deps** unless absolutely necessary (host has
  had recurring issues with extension/dep resolution).
- **Egyptian 5-year tax retention** for financial records — GDPR
  erase preserves rows and overwrites PII fields instead of
  hard-deleting.
