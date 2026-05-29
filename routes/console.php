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

// Trial expiry notifications — runs every hour so a trial that ends
// at 14:23 gets the "ending soon" mail by 15:00 at the latest.
Schedule::command('trials:check')->hourly();

// Database backup — nightly at 03:00 server time, when traffic is
// lowest. 14-day retention is configurable in the command's options.
Schedule::command('db:backup --keep=14')
    ->dailyAt('03:00')
    ->onOneServer()
    ->runInBackground()
    ->withoutOverlapping();

// Dunning loop — runs daily at 09:00 server time so emails land
// while customers are checking inboxes, not at 3 AM. The state
// machine is idempotent per day so re-running is safe.
Schedule::command('billing:dunning')
    ->dailyAt('09:00')
    ->onOneServer()
    ->withoutOverlapping();

// Trial welcome drip — runs once a day at 10:00 server time. Walks
// the 3-step drip (welcome / tour / case study) and advances each
// active trial at most one step per run. Idempotent.
Schedule::command('trials:drip')
    ->dailyAt('10:00')
    ->onOneServer()
    ->withoutOverlapping();

// Retention pruner — daily at 03:30, after the backup window so the
// backup captures the full dataset before the prune removes anything.
// Trims activity_logs (365d), failed_jobs (30d), sessions (30d),
// customer_login_tokens (7d). Permanent actions preserved.
Schedule::command('maint:prune')
    ->dailyAt('03:30')
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
