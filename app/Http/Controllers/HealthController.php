<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * `/healthz` — the JSON liveness + readiness check that uptime
 * monitors (UptimeRobot, HealthChecks.io, Pingdom, Cloudflare
 * Health Checks) hit on a 1-minute cadence.
 *
 * Two endpoints expose what most monitors look for:
 *   GET /healthz       — fast, 200 if the process is up at all
 *   GET /healthz/deep  — checks DB + cache + storage, slower
 *
 * The deep check intentionally returns 200 even if a dependency is
 * sad, with the failure surfaced in the body. Returning 503 is
 * correct semantically but causes some monitor configurations to
 * mark the whole site down for a transient cache write error. We
 * pay the cost of richer body parsing in the monitor in exchange
 * for not getting paged for non-customer-facing flakes.
 */
class HealthController extends Controller
{
    public function shallow(): JsonResponse
    {
        // Pure process liveness — if PHP is running, return 200.
        return response()->json([
            'status' => 'ok',
            'time' => now()->toIso8601String(),
        ]);
    }

    public function deep(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache'    => $this->checkCache(),
            'storage'  => $this->checkStorage(),
            'queue'    => $this->checkQueue(),
        ];

        $allUp = collect($checks)->every(fn ($c) => $c['status'] === 'ok');

        return response()->json([
            'status' => $allUp ? 'ok' : 'degraded',
            'time' => now()->toIso8601String(),
            'checks' => $checks,
            'version' => $this->version(),
        ]);
    }

    protected function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            return [
                'status' => 'ok',
                'latency_ms' => round((microtime(true) - $start) * 1000, 1),
            ];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'error' => $e->getMessage()];
        }
    }

    protected function checkCache(): array
    {
        try {
            $key = 'healthz-probe';
            $val = (string) microtime(true);
            $start = microtime(true);
            Cache::put($key, $val, 30);
            $read = Cache::get($key);
            return [
                'status' => $read === $val ? 'ok' : 'degraded',
                'driver' => config('cache.default'),
                'latency_ms' => round((microtime(true) - $start) * 1000, 1),
            ];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'error' => $e->getMessage()];
        }
    }

    protected function checkStorage(): array
    {
        try {
            $disk = Storage::disk('local');
            $start = microtime(true);
            $disk->put('healthz-probe.txt', (string) now());
            $disk->delete('healthz-probe.txt');
            return [
                'status' => 'ok',
                'latency_ms' => round((microtime(true) - $start) * 1000, 1),
            ];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'error' => $e->getMessage()];
        }
    }

    protected function checkQueue(): array
    {
        try {
            // Just report current backlog — don't enqueue a probe job
            // because that'd grow the failed_jobs table over time
            // if the worker is dead.
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            return [
                'status' => 'ok',
                'pending' => $pending,
                'failed' => $failed,
            ];
        } catch (\Throwable $e) {
            return ['status' => 'down', 'error' => $e->getMessage()];
        }
    }

    protected function version(): ?string
    {
        // Useful for "is the new deploy live yet?" — reads from a
        // file the deploy script writes after a successful pull.
        $path = base_path('.deploy-version');
        return is_readable($path) ? trim((string) file_get_contents($path)) : null;
    }
}
