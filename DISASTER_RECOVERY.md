# Disaster Recovery Runbook

Audience: on-call operator restoring Doctorato after a host failure,
data loss event, or compromised credential.

Recovery Time Objective (RTO): **4 hours** to a working read-only site,
**24 hours** to full write capability.
Recovery Point Objective (RPO): **24 hours** (nightly backup cadence).

---

## 1. Decision Tree (read first)

```
Is the site reachable at all?
├── NO  → Section 2  (host outage)
└── YES
    ├── Is the DB returning data?
    │   ├── NO  → Section 3  (DB corruption / data loss)
    │   └── YES
    │       ├── Are payments processing?  → if NO → Section 4
    │       └── Are emails going out?     → if NO → Section 5
    └── Suspected credential compromise   → Section 6
```

---

## 2. Host outage (Cloudflare can't reach origin)

1. Confirm via `https://www.cloudflarestatus.com` that Cloudflare itself
   is healthy. If not, wait — there is nothing to do at the origin.
2. SSH into cPanel. If SSH is dead, log into the cPanel web console.
3. Check `tail -200 /home/<user>/public_html/storage/logs/laravel.log`
   for the trailing error.
4. If Apache is the issue, restart from cPanel → Service Status.
5. If MySQL is the issue, see Section 3.
6. If the host is fully offline, point the Cloudflare DNS record at the
   static maintenance bucket (S3/R2) and post on the
   [status page](https://doctorato.com/status).

---

## 3. Database corruption / data loss

### 3.1 Locate the most recent backup

Backups are produced by `php artisan db:backup` (in
`app/Console/Commands/BackupDatabase.php`) and stored in
`storage/app/backups/`. Retention is 14 days.

```bash
ls -lh storage/app/backups/ | tail -20
```

Each file is named `doctorato-YYYY-MM-DD-HHMMSS.sql.gz`.

### 3.2 Restore

```bash
# 1. Take the app OFFLINE so writes don't hit the corrupted DB
php artisan down --secret="recovery-only-token"

# 2. Snapshot the corrupted DB (forensics — DO NOT skip even if
#    obviously broken; you'll need it for the post-mortem)
mysqldump -u <user> -p <db_name> > corrupted-$(date +%F).sql

# 3. Drop + recreate
mysql -u <user> -p -e "DROP DATABASE <db_name>; CREATE DATABASE <db_name>;"

# 4. Restore the most recent good backup
gunzip -c storage/app/backups/doctorato-YYYY-MM-DD-HHMMSS.sql.gz | \
    mysql -u <user> -p <db_name>

# 5. Run any pending migrations (idempotent — Schema::hasColumn guards
#    in every migration since Phase 1 mean re-running is safe)
php artisan migrate --force

# 6. Clear cached app state
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 7. Bring the site back up
php artisan up
```

### 3.3 Reconcile the gap

The RPO is 24h, so any data created between the backup snapshot and
the failure is gone. Required next steps:

1. Check the Paymob dashboard for payments captured AFTER the backup
   timestamp that are NOT in the restored DB. Re-create the Payment +
   Invoice rows manually using `php artisan tinker`.
2. Customers who submitted /demo or /contact in that window may need a
   manual reach-out. Pull the source-of-truth from inbound email logs.
3. Subscriptions: ask Paymob support for transaction list, cross-check
   against `subscriptions.starts_at` vs Paymob `created_at`.

---

## 4. Payments not processing

### 4.1 Verify Paymob keys

```bash
php artisan tinker
>>> config('services.paymob.api_key')
>>> config('services.paymob.hmac')
```

Empty value → check `.env` was not wiped by the failed deploy.
Wrong value → rotate from the Paymob dashboard.

### 4.2 Verify webhook URL in Paymob dashboard

Should be `https://doctorato.com/webhooks/paymob`. If pointing to an
old domain, fix it there.

### 4.3 Check `failed_jobs` for queued mail/transactions

```bash
php artisan queue:health
```

Stuck jobs → `php artisan queue:retry all`.

### 4.4 Manually replay a failed webhook

Paymob will retry on non-200 for ~24h. If we need to force one through
NOW, get the raw POST body from the Paymob dashboard and replay with
`curl` against `/webhooks/paymob?hmac=<original-hmac>`.

---

## 5. Emails not going out

1. `php artisan queue:health` — check `failed_jobs` count.
2. `tail storage/logs/laravel.log | grep mail.` — look for SMTP errors.
3. Common: SMTP password expired. Update `.env` `MAIL_PASSWORD` then
   `php artisan config:clear`.
4. Hard cap on host: ask cPanel support if we've hit the daily send
   quota. If yes, escalate the customer email channel to a transactional
   provider (Postmark / SES) — emergency fallback is documented in
   `OPERATIONS.md §"Email migration"`.

---

## 6. Credential compromise

If a credential is suspected to have leaked (admin password, Paymob key,
SMTP password, DB password):

1. **DO NOT touch the leaked credential first.** Capture forensics:
   ```bash
   tail -10000 storage/logs/laravel.log > /tmp/forensics-$(date +%s).log
   mysqldump login_attempts activity_logs > /tmp/forensics-auth.sql
   ```
2. Rotate the credential at its source (Paymob dashboard, cPanel email
   account, DB user password via cPanel MySQL section).
3. Update `.env` on the server.
4. `php artisan config:clear && php artisan cache:clear`.
5. For admin password: also force-logout all admin sessions:
   ```bash
   php artisan tinker
   >>> DB::table('sessions')->delete();
   ```
6. Review `login_attempts` for the last 48h:
   ```bash
   php artisan tinker
   >>> DB::table('login_attempts')
   ...     ->where('attempted_at', '>=', now()->subDays(2))
   ...     ->where('success', true)
   ...     ->get();
   ```
   Anything that doesn't match a known admin IP gets investigated.
7. Post-mortem: write up the timeline, root cause, and the
   actions taken. File under `docs/incidents/YYYY-MM-DD-summary.md`.

---

## 7. Post-recovery checklist

After any incident:

- [ ] Site responds 200 on `/` and `/admin/login`
- [ ] `/healthz` returns `ok`
- [ ] `/healthz/deep` returns all green
- [ ] A test demo submission succeeds end-to-end
- [ ] A test customer login (magic link) is received
- [ ] Queue worker is processing (`php artisan queue:health`)
- [ ] Scheduler is firing (look for `maint:prune` log entry in last 24h)
- [ ] Backup ran the night after recovery (`ls storage/app/backups/`)
- [ ] Post-mortem doc started in `docs/incidents/`
- [ ] Status page updated to "resolved"

---

## 8. Contact escalation order

1. Primary on-call: `info@doctorato.com`
2. Host (cPanel): see hosting account email
3. Paymob support: via dashboard ticket system
4. DNS (Cloudflare): account dashboard ticket
5. Legal (for breaches affecting customer PII): outside counsel
   contact in `OPERATIONS.md §"Compliance contacts"`

---

## 9. Backup verification (perform monthly)

The backup is only as good as the last restore drill. Schedule the 1st
Saturday of each month:

```bash
# Spin up a fresh empty DB
mysql -u <user> -p -e "CREATE DATABASE doctorato_restore_test;"

# Restore latest backup into it
gunzip -c storage/app/backups/$(ls -t storage/app/backups/ | head -1) | \
    mysql -u <user> -p doctorato_restore_test

# Spot-check row counts
mysql -u <user> -p doctorato_restore_test -e "
    SELECT 'subscriptions' AS t, COUNT(*) FROM subscriptions
    UNION SELECT 'invoices', COUNT(*) FROM invoices
    UNION SELECT 'payments', COUNT(*) FROM payments
    UNION SELECT 'demo_requests', COUNT(*) FROM demo_requests;
"

# Clean up
mysql -u <user> -p -e "DROP DATABASE doctorato_restore_test;"
```

If counts look reasonable (within 5% of production), the backup is
healthy. If not, escalate to the operations channel immediately —
silent backup corruption is the worst kind of failure.
