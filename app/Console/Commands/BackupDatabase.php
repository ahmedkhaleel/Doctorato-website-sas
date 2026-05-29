<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

/**
 * `php artisan db:backup`
 *
 * Self-contained nightly database backup for shared hosting where
 * we can't (yet) install spatie/laravel-backup and don't have an S3
 * bucket configured. Writes a gzipped mysqldump to
 * storage/app/backups/ with a date-stamped filename, and rotates
 * out old copies so the directory doesn't grow forever.
 *
 * Designed to be cron-callable:
 *   0 3 * * * cd ~/public_html && php artisan db:backup --quiet
 *
 * Restoration:
 *   gunzip -c storage/app/backups/doctorato-2026-05-16.sql.gz | \
 *     mysql -u root -p doctorato
 *
 * Once S3/R2 access is available, swap the local write for a
 * Storage::disk('s3')->put() call — the dump generation stays the
 * same, only the destination changes.
 */
class BackupDatabase extends Command
{
    protected $signature = 'db:backup
        {--keep=14 : Number of daily backups to retain}
        {--dir=backups : Subdirectory under storage/app/ to write into}';

    protected $description = 'Dumps the database to a gzipped SQL file and prunes old copies.';

    public function handle(): int
    {
        $keep = max(1, (int) $this->option('keep'));
        $relDir = trim((string) $this->option('dir'), '/');
        $dir = storage_path('app/' . $relDir);
        File::ensureDirectoryExists($dir, 0755, true);

        $date = Carbon::now()->format('Y-m-d_His');
        $name = ($this->databaseName() ?: 'database') . "-{$date}.sql.gz";
        $path = $dir . '/' . $name;

        if (!$this->dump($path)) {
            $this->error('Backup failed. See output above.');
            return self::FAILURE;
        }

        $size = $this->humanSize(filesize($path) ?: 0);
        $this->info("Backup written: {$path} ({$size})");

        $removed = $this->rotate($dir, $keep);
        if ($removed > 0) {
            $this->line("Rotated out {$removed} old backup(s) (keep last {$keep}).");
        }

        return self::SUCCESS;
    }

    /**
     * Run mysqldump piped through gzip to the target path. Uses the
     * connection details Laravel itself parsed from .env so we don't
     * duplicate config. Falls through to a PHP-side dump if the
     * mysqldump binary isn't on the path (shared-hosting fallback).
     */
    protected function dump(string $path): bool
    {
        $cfg = config('database.connections.' . config('database.default'));
        $cmd = sprintf(
            "mysqldump --single-transaction --quick --skip-lock-tables -h%s -P%s -u%s -p%s %s | gzip > %s",
            escapeshellarg($cfg['host'] ?? '127.0.0.1'),
            escapeshellarg((string) ($cfg['port'] ?? '3306')),
            escapeshellarg($cfg['username'] ?? 'root'),
            escapeshellarg($cfg['password'] ?? ''),
            escapeshellarg($cfg['database']),
            escapeshellarg($path)
        );

        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout(600); // 10 minutes is plenty for our size
        $process->run();

        if (!$process->isSuccessful()) {
            // mysqldump not available, or auth failed — try the
            // pure-PHP fallback which dumps via the existing PDO
            // connection. Slower, but works on hosts where shell
            // execs are restricted.
            $this->warn('mysqldump unavailable, falling back to PHP dumper.');
            return $this->phpDump($path);
        }
        return true;
    }

    /**
     * Pure-PHP fallback: walks every table, writes CREATE + INSERTs
     * to a gzip stream. Slower than mysqldump but works on any
     * cPanel install with PDO + zlib (both standard).
     */
    protected function phpDump(string $path): bool
    {
        $gz = gzopen($path, 'wb9');
        if ($gz === false) {
            $this->error("Cannot open {$path} for writing.");
            return false;
        }
        try {
            gzwrite($gz, "-- Doctorato DB backup " . date('c') . "\n");
            gzwrite($gz, "SET FOREIGN_KEY_CHECKS=0;\n");
            $tables = array_column(DB::select('SHOW TABLES'), 'Tables_in_' . config('database.connections.' . config('database.default') . '.database'));
            foreach ($tables as $table) {
                $create = DB::selectOne("SHOW CREATE TABLE `{$table}`");
                $createSql = $create->{'Create Table'} ?? null;
                if ($createSql) {
                    gzwrite($gz, "\nDROP TABLE IF EXISTS `{$table}`;\n");
                    gzwrite($gz, $createSql . ";\n");
                }
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $cols = array_map(fn ($k) => "`{$k}`", array_keys((array) $row));
                    $vals = array_map(
                        fn ($v) => $v === null ? 'NULL' : DB::getPdo()->quote((string) $v),
                        array_values((array) $row)
                    );
                    gzwrite($gz, "INSERT INTO `{$table}` (" . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ");\n");
                }
            }
            gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n");
        } finally {
            gzclose($gz);
        }
        return true;
    }

    /**
     * Keep the most recent N backups, delete the rest. Sorted by
     * mtime so a manually-restored older backup written later doesn't
     * survive accidentally.
     */
    protected function rotate(string $dir, int $keep): int
    {
        $files = collect(glob($dir . '/*.sql.gz') ?: [])
            ->sortByDesc(fn ($f) => filemtime($f) ?: 0)
            ->values();
        $toDelete = $files->slice($keep);
        foreach ($toDelete as $f) {
            @unlink($f);
        }
        return $toDelete->count();
    }

    protected function databaseName(): ?string
    {
        return config('database.connections.' . config('database.default') . '.database');
    }

    protected function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $v = (float) $bytes;
        while ($v >= 1024 && $i < count($units) - 1) {
            $v /= 1024;
            $i++;
        }
        return sprintf('%.2f %s', $v, $units[$i]);
    }
}
