<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| All cron-driven jobs declared here. Activated by a single system
| cron entry on the cPanel host:
|
|   * * * * * cd ~/public_html && /opt/cpanel/ea-php84/root/usr/bin/php \
|     artisan schedule:run >> /dev/null 2>&1
|
| Laravel's scheduler dispatches the individual tasks below at the
| times each one specifies — you don't need a separate cron per task.
*/

/*
 * Cron jitter note
 * ----------------
 * The daily/hourly tasks below all run at a fixed wall-clock time.
 * That's fine when the host is dedicated to one Laravel app, but on
 * shared cPanel everyone's cron tries to fire at :00 / :30 of the
 * hour at once, which is the "thundering herd" — CPU spikes, MySQL
 * thread pool gets exhausted, and our task waits or fails.
 *
 * We mitigate two ways without touching the cron entry itself:
 *   1. ->runInBackground() on long tasks so schedule:run isn't
 *      blocked by mysqldump while OTHER tasks miss their slot.
 *   2. Stagger each daily task by a different MINUTE (e.g. backup
 *      at 03:00, prune at 03:30, dunning at 09:07, drip at 10:13)
 *      so they don't collide with each other or the host's other
 *      tenants' rounded-time crons.
 *
 * If you add a new daily task: pick a minute that ISN'T already in
 * use AND ISN'T 00, 15, 30, or 45 (the standard busy slots).
 */

// Database backup — nightly at 03:00 server time, when traffic is
// lowest. 14-day retention is configurable in the command's options.
Schedule::command('db:backup --keep=14')
    ->dailyAt('03:00')
    ->onOneServer()
    ->runInBackground()
    ->withoutOverlapping();

// Dunning loop — daily at 09:07 (jittered off :00 to avoid the
// host-wide cron pile-up). Emails land while customers are checking
// inboxes, not at 3 AM. State machine is idempotent per day.
Schedule::command('billing:dunning')
    ->dailyAt('09:07')
    ->onOneServer()
    ->withoutOverlapping();

// Trial welcome drip — daily at 10:13 (jittered). Walks the 3-step
// drip (welcome / tour / case study), advancing each active trial
// at most one step per run. Idempotent.
Schedule::command('trials:drip')
    ->dailyAt('10:13')
    ->onOneServer()
    ->withoutOverlapping();

// Retention pruner — daily at 03:37 (after backup, jittered off the
// host-busy :30 slot). Trims activity_logs (365d), failed_jobs (30d),
// sessions (30d), customer_login_tokens (7d). Permanent actions preserved.
Schedule::command('maint:prune')
    ->dailyAt('03:37')
    ->onOneServer()
    ->withoutOverlapping();

// Trial expiry/ending-soon — runs once an hour at :11 (off the busy
// :00 slot). A trial ending at 14:23 gets the heads-up by 15:11.
Schedule::command('trials:check')->cron('11 * * * *');

// Metrics snapshot — daily at 23:55, just before midnight, so the
// snapshot captures the "end of day" state. Idempotent.
Schedule::command('metrics:snapshot')
    ->dailyAt('23:55')
    ->onOneServer()
    ->withoutOverlapping();

// Auto-resume paused subscriptions — daily at 02:23 (jittered).
// Walks subs where paused_until <= now and clears both pause cols.
// Idempotent.
Schedule::command('subs:auto-resume')
    ->dailyAt('02:23')
    ->onOneServer()
    ->withoutOverlapping();

// Queue health check — every 15 minutes. If the worker is dead or
// the queue is backing up, exits non-zero. Pair this with an
// external monitor (HealthChecks.io, UptimeRobot) for paging.
Schedule::command('queue:health')
    ->everyFifteenMinutes()
    ->onOneServer()
    ->withoutOverlapping();

// Queue worker — kicked every minute, stops itself after 55s so the
// next minute's cron picks up cleanly. --tries=3 lets transient SMTP
// failures retry instead of going straight to failed_jobs.
Schedule::command('queue:work --stop-when-empty --max-time=55 --tries=3')
    ->everyMinute()
    ->onOneServer()
    ->withoutOverlapping();
