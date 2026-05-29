<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * Public `/status` — single-glance system health for customers and
 * prospects. Trust signal during outages (people see we're aware)
 * and a baseline for "is it just me?" support tickets.
 *
 * Backed by the same probes /healthz/deep uses, but rendered as a
 * branded page instead of JSON. Cached for 60 seconds so a hot link
 * on Twitter can't DDoS the DB.
 */
class StatusPageController extends Controller
{
    public function show(Request $request)
    {
        $checks = Cache::remember('status.snapshot', 60, function () {
            return [
                'website'  => $this->checkWebsite(),
                'database' => $this->checkDatabase(),
                'queue'    => $this->checkQueue(),
                'storage'  => $this->checkStorage(),
            ];
        });

        $overall = collect($checks)->every(fn ($c) => $c['status'] === 'operational') ? 'operational' : 'degraded';

        return Inertia::render('Status', [
            'overall' => $overall,
            'checks' => $checks,
            'lastChecked' => now()->toIso8601String(),
        ]);
    }

    protected function checkWebsite(): array
    {
        // If we're rendering this response, the web stack is up.
        return ['name' => 'Website', 'status' => 'operational', 'description' => 'doctorato.com is responding normally.'];
    }

    protected function checkDatabase(): array
    {
        try {
            DB::select('SELECT 1');
            return ['name' => 'Database', 'status' => 'operational', 'description' => 'Read and write queries are running normally.'];
        } catch (\Throwable $e) {
            return ['name' => 'Database', 'status' => 'down', 'description' => 'Database connection failed.'];
        }
    }

    protected function checkQueue(): array
    {
        try {
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            if ($failed > 50) {
                return ['name' => 'Background jobs', 'status' => 'degraded', 'description' => "Elevated job failure rate ({$failed})."];
            }
            if ($pending > 500) {
                return ['name' => 'Background jobs', 'status' => 'degraded', 'description' => "Queue backlog is high ({$pending}). Email delivery may be slow."];
            }
            return ['name' => 'Background jobs', 'status' => 'operational', 'description' => 'Email and webhooks are processing normally.'];
        } catch (\Throwable) {
            return ['name' => 'Background jobs', 'status' => 'down', 'description' => 'Cannot read the queue state.'];
        }
    }

    protected function checkStorage(): array
    {
        try {
            $path = storage_path('app/healthz-status.txt');
            file_put_contents($path, (string) now());
            @unlink($path);
            return ['name' => 'File storage', 'status' => 'operational', 'description' => 'Uploads and attachments are working.'];
        } catch (\Throwable) {
            return ['name' => 'File storage', 'status' => 'degraded', 'description' => 'File writes may be slow or failing.'];
        }
    }
}
