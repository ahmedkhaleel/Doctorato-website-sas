# Doctorato — Operations Handbook

The day-to-day "things break, how do I fix them" reference for anyone
running the production site. Keep this open in a tab while on call.

---

## 1. The single cron entry

The production host needs exactly ONE cron line. Laravel's scheduler
dispatches every periodic task from there.

```cron
* * * * * cd ~/public_html && /opt/cpanel/ea-php84/root/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

What this runs internally (declared in `routes/console.php`):

| Task                     | When            | Why                                                   |
| ------------------------ | --------------- | ----------------------------------------------------- |
| `queue:work`             | every minute    | Drains the email queue                                |
| `queue:health`           | every 15 min    | Exits non-zero on backlog/stuck/failures              |
| `trials:check`           | hourly          | Emails customers whose trials end soon                |
| `billing:dunning`        | 09:00 daily     | Reminders → past_due → cancel for failed invoices     |
| `db:backup --keep=14`    | 03:00 daily     | gzipped SQL dump to `storage/app/backups/`            |

---

## 2. Deploys

`bash deploy.sh` pulls origin/main, runs composer, migrates, clears cache.

After every deploy:

```bash
cd ~/public_html
bash deploy.sh
php artisan migrate --force        # if a migration was in the commit
php artisan optimize:clear         # routes + config + view caches
```

The deploy script writes the current SHA to `.deploy-version` which
`/healthz/deep` reports — handy for "is the new code live?" checks.

---

## 3. The queue

Queued jobs run via `php artisan queue:work` (scheduled every minute).

| Symptom                                | Diagnose with                        | Fix                                                       |
| -------------------------------------- | ------------------------------------ | --------------------------------------------------------- |
| Emails not arriving                    | `php artisan queue:health`           | Check if worker cron is running (`crontab -l`)            |
| `jobs` table growing                   | `php artisan queue:health`           | Worker stalled; manually run `php artisan queue:work --once` and watch output |
| `failed_jobs` table growing            | `php artisan queue:failed`           | Read the exception; retry with `php artisan queue:retry all` |

To purge a stuck `failed_jobs` table after fixing the cause:

```bash
php artisan queue:flush
```

---

## 4. Backups

Backups land in `storage/app/backups/` as `doctorato-YYYY-MM-DD_HHMMSS.sql.gz`.

**Restore drill (run this once a quarter):**

```bash
# Copy the dump to a working dir
cp storage/app/backups/doctorato-2026-05-16_*.sql.gz /tmp/

# Decompress
gunzip /tmp/doctorato-*.sql.gz

# Restore into a scratch database
mysql -u root -p -e 'CREATE DATABASE doctorato_restore_test;'
mysql -u root -p doctorato_restore_test < /tmp/doctorato-*.sql

# Spot-check
mysql -u root -p doctorato_restore_test -e 'SELECT COUNT(*) FROM users; SELECT COUNT(*) FROM subscriptions;'

# Clean up
mysql -u root -p -e 'DROP DATABASE doctorato_restore_test;'
```

If the restore fails, the backup pipeline is broken — investigate before
the next disaster.

To copy backups off-server (do this at least weekly until proper S3 is wired):

```bash
# From your local machine
rsync -avz doctorato@srv.your-host.com:~/public_html/storage/app/backups/ ./local-backups/
```

---

## 5. Monitoring

### `/healthz`
Returns `{ status: ok }` if PHP is up. Configure UptimeRobot (free tier) to
ping every 5 minutes:

```
https://doctorato.com/healthz
```

### `/healthz/deep`
Returns full subsystem state. Use for richer alerting:

```bash
curl https://doctorato.com/healthz/deep | jq
```

### `/status`
Public, branded. Share with customers during incidents.

### Server-side error notifications

`AdminExceptionNotifier` mails `info@doctorato.com` on uncaught production
exceptions, throttled to one email per signature per hour. Validation /
404 / throttle / auth exceptions are filtered out — they're noise.

Check the inbox; the signature in the subject line tells you whether
you're looking at the same bug or a new one.

---

## 6. Common operational tasks

### Send a customer a fresh login link

```bash
php artisan tinker
>>> [$token, $plain] = \App\Models\CustomerLoginToken::issue('customer@example.com');
>>> echo url('/portal/auth/' . $plain) . PHP_EOL;
```

Email or message that URL to the customer. 15-minute TTL, single-use.

### Disable an admin

```bash
php artisan tinker
>>> \App\Models\User::where('email', 'ex-employee@doctorato.com')->update(['is_active' => false]);
```

Effective immediately — their next request hits the is_active gate in
`AuthController::login`.

### Force-cancel a subscription mid-billing

```bash
php artisan tinker
>>> $sub = \App\Models\Subscription::find(123);
>>> $sub->update(['status' => 'canceled', 'canceled_at' => now(), 'ends_at' => now()]);
```

(Sets ends_at to now so they lose access immediately rather than at the
end of the billing cycle.)

### Replay a failed Paymob webhook

```bash
php artisan tinker
>>> $payment = \App\Models\Payment::where('gateway_order_id', 'X')->first();
>>> $payment->update(['gateway_transaction_id' => null, 'processed_at' => null]);
# now Paymob's retry will pass the idempotency guard
```

### Inspect activity for a specific user

```bash
php artisan tinker
>>> \App\Models\ActivityLog::where('user_id', 5)->latest()->limit(10)->get(['action', 'subject_type', 'subject_label', 'created_at']);
```

Or use the admin UI: `/admin/activity-logs?user=5`.

---

## 7. Permission model

Roles (defined in `User::roles()`):

| Role         | Scope                                                       |
| ------------ | ----------------------------------------------------------- |
| `super_admin`| Full access; bypasses `hasPermission()`                     |
| `admin`      | Full access; bypasses `hasPermission()`                     |
| `manager`    | Content + leads (FAQs, testimonials, contacts, demos)       |
| `editor`     | Content only (FAQs, testimonials)                           |
| `viewer`     | Dashboard read-only                                         |

Permissions on the route definition use `->middleware('admin.perm:KEY')`.
KEYs map to `User::availablePermissions()`. Adding a new permission:
1. Add the key + label to `availablePermissions()`
2. Add it to the role defaults in `defaultPermissionsForRole()`
3. Add `->middleware('admin.perm:KEY')` to the relevant routes
4. The admin UI for managing custom per-user overrides reads from
   the same source-of-truth, so no extra wiring needed.

---

## 8. 2FA recovery for an admin who lost their device

```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'admin@doctorato.com')->first();
>>> $user->forceFill([
...     'two_factor_secret' => null,
...     'two_factor_recovery_codes' => null,
...     'two_factor_confirmed_at' => null,
... ])->save();
```

Next login skips the 2FA challenge. The admin should re-enroll
immediately via `/admin/2fa`.

---

## 9. Cache

Default cache driver is `file` (declared in `config/cache.php` default).
Stored under `storage/framework/cache/`.

Clearing:
```bash
php artisan cache:clear
```

`PublicContentCache` (pricing plans, FAQs, add-ons, testimonials) is
auto-busted by the model observers, so admin edits show up on the next
page load without manual flushing.

---

## 10. Logs

| File                                       | Contents                                       |
| ------------------------------------------ | ---------------------------------------------- |
| `storage/logs/laravel.log`                 | Application log: errors, info, warnings        |
| `storage/logs/queue.log`                   | Worker stdout (if cron redirects to it)        |
| `storage/logs/deploy-webhook.log`          | GitHub deploy webhook hits                     |

Tail the last 50 lines:
```bash
tail -50 storage/logs/laravel.log
```

Filter by topic (auth events, recaptcha, geo, etc.):
```bash
grep -E "auth\.|recaptcha|geoip" storage/logs/laravel.log | tail -50
```

---

## 11. Tests

```bash
php artisan test            # full suite
php artisan test --filter=Webhook   # one file
```

The suite is structured as `tests/Feature/*Test.php` — see those for
examples when writing new tests.

---

## 12. The list of "if it breaks, this is why" knowledge

- **Forms returning "تعذر التحقق من الرسالة"** → the bot-defense fields
  (`hp_trap`, `form_rendered_at`, `recaptcha_token`) aren't in the form
  payload. Check the Vue page's `useForm({...})` block.
- **"Geo lookup: GuzzleHttp requires cURL"** → the PHP version Apache
  is using doesn't have the curl extension. Enable it in cPanel →
  Select PHP Version. The browser-side fallback in
  `ClientCountryDetector.vue` keeps the site working in the meantime.
- **Country flag/currency stuck on EG after traveling** → user's session
  has `active_country_source = 'explicit'`. They should visit
  `/country/reset` or clear their cookies.
- **Paymob webhook returning 400** → HMAC mismatch. Check the `hmac`
  query param matches what `PaymobService::verifyHmac` expects with
  the current secret.
- **`Cannot access protected property Illuminate\Mail\Message::$message`
  in a mail render** → the mailable's view variable is named `message`,
  which clobbers Laravel's built-in. Rename to anything else.

---

## 13. People to escalate to

- **Hosting / DNS** — your cPanel provider
- **Payments** — Paymob support (support@paymob.com)
- **Email delivery** — start with `MAIL_*` in `.env`; consider Resend
  if cPanel SMTP delivery rate drops below 95%
- **DDoS / abuse** — Cloudflare (once activated)
