<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Self-contained error-monitor "lite" — emails the admin when an
 * unexpected exception fires in production. Built because Sentry-
 * style hosted monitoring needs cURL + outbound HTTPS which the
 * current cPanel install has had recurring trouble with.
 *
 * Rate limiting: a given exception signature (class + file + line)
 * is throttled to at most ONE email per hour. Without this, a bad
 * loop that throws on every request would flood the inbox in
 * minutes.
 *
 * Filters: HTTP 4xx exceptions (ValidationException, NotFoundException,
 * AuthorizationException, etc.) are NEVER paged on — those are
 * normal user behavior, not bugs.
 */
class AdminExceptionNotifier
{
    /**
     * Skip notifying for these exception classes. They represent
     * normal user flow or expected outcomes, not bugs.
     */
    protected const SKIP_CLASSES = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Http\Exceptions\ThrottleRequestsException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class,
    ];

    /** Throttle window in seconds for the same signature. */
    protected const COOLDOWN_SECONDS = 3600;

    /** Email destination. */
    protected const TARGET_EMAIL = 'info@doctorato.com';

    public function notify(Throwable $e, ?array $context = null): void
    {
        // Only fire in production. In local/staging the log channel
        // is enough — we don't want noise during development.
        if (!app()->isProduction()) {
            return;
        }

        if ($this->shouldSkip($e)) {
            return;
        }

        $signature = $this->signatureFor($e);
        $cacheKey = 'exception_notify:' . $signature;
        if (Cache::has($cacheKey)) {
            // Within the cooldown — log the recurrence count so we
            // know it's still happening, but don't email again.
            Cache::increment("exception_count:{$signature}");
            return;
        }

        Cache::put($cacheKey, true, self::COOLDOWN_SECONDS);
        Cache::put("exception_count:{$signature}", 1, self::COOLDOWN_SECONDS);

        try {
            $this->send($e, $context, $signature);
        } catch (Throwable $sendErr) {
            // Last-resort: log so we don't silently swallow the
            // failure to notify about a failure. Don't recurse —
            // we never call notify() from here.
            Log::error('AdminExceptionNotifier failed to send', [
                'original_error' => $e->getMessage(),
                'send_error' => $sendErr->getMessage(),
            ]);
        }
    }

    protected function shouldSkip(Throwable $e): bool
    {
        foreach (self::SKIP_CLASSES as $skip) {
            if ($e instanceof $skip) return true;
        }
        return false;
    }

    /**
     * Email a non-exception signal (e.g. a portal abuse signal).
     * Reuses the same signature throttling so a runaway detector
     * can't fill the admin inbox. Skipped in non-production.
     */
    public function notifySignal(string $signalName, array $context = []): void
    {
        if (!app()->isProduction()) {
            return;
        }

        $signature = 'sig:' . substr(sha1($signalName), 0, 10);
        $cacheKey = 'exception_notify:' . $signature;
        if (Cache::has($cacheKey)) {
            Cache::increment("exception_count:{$signature}");
            return;
        }
        Cache::put($cacheKey, true, self::COOLDOWN_SECONDS);
        Cache::put("exception_count:{$signature}", 1, self::COOLDOWN_SECONDS);

        try {
            $body = "═══ Doctorato Signal ═══\n\n"
                . "Signal: {$signalName}\n"
                . "Time:   " . now()->toIso8601String() . "\n"
                . "Signature: {$signature} (next email: at most one per hour)\n\n"
                . "Context:\n" . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            Mail::raw($body, function ($msg) use ($signalName) {
                $msg->to(self::TARGET_EMAIL)
                    ->subject('[Doctorato] Signal: ' . $signalName);
            });
        } catch (Throwable $e) {
            Log::error('AdminExceptionNotifier::notifySignal failed', [
                'signal' => $signalName, 'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Deterministic identifier for this exception's "shape". Two
     * exceptions with the same class + file + line are treated as
     * the same incident for throttling purposes.
     */
    protected function signatureFor(Throwable $e): string
    {
        return substr(sha1(implode('|', [
            get_class($e),
            $e->getFile() ?? '',
            $e->getLine() ?? '',
        ])), 0, 12);
    }

    protected function send(Throwable $e, ?array $context, string $signature): void
    {
        $body = $this->renderBody($e, $context, $signature);

        Mail::raw($body, function ($msg) use ($e) {
            $msg->to(self::TARGET_EMAIL)
                ->subject('[Doctorato] ' . class_basename($e) . ': ' . substr($e->getMessage(), 0, 80));
        });
    }

    protected function renderBody(Throwable $e, ?array $context, string $signature): string
    {
        $req = request();
        $user = $req?->user();

        $parts = [
            "═══ Doctorato Exception ═══",
            "",
            "Signature: {$signature}  (next email about this exact line: at most one per hour)",
            "Class:     " . get_class($e),
            "Message:   " . $e->getMessage(),
            "File:      " . $e->getFile() . ':' . $e->getLine(),
            "Time:      " . now()->toIso8601String(),
            "",
            "─── Request ───",
            "URL:       " . ($req?->fullUrl() ?? '(CLI)'),
            "Method:    " . ($req?->method() ?? '—'),
            "IP:        " . ($req?->ip() ?? '—'),
            "User:      " . ($user ? "{$user->id} ({$user->email})" : 'guest'),
            "User-Agent:" . ($req?->userAgent() ?? '—'),
        ];

        if ($context) {
            $parts[] = "";
            $parts[] = "─── Context ───";
            $parts[] = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $parts[] = "";
        $parts[] = "─── Stack (top 20 frames) ───";
        $trace = explode("\n", $e->getTraceAsString());
        $parts[] = implode("\n", array_slice($trace, 0, 20));

        return implode("\n", $parts);
    }
}
