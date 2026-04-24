<?php
/**
 * GitHub webhook → auto-deploy trigger.
 *
 * Place at: https://doctorato.com/deploy.php
 *
 * GitHub setup:
 *   repo Settings → Webhooks → Add webhook
 *     Payload URL:   https://doctorato.com/deploy.php
 *     Content type:  application/json
 *     Secret:        (set DEPLOY_WEBHOOK_SECRET env var to the same value)
 *     Events:        Just the push event
 *
 * How it works (no shell exec from the web request — safer on shared hosting):
 *   1. Verify HMAC signature + branch filter.
 *   2. Touch a trigger file in storage/framework/.deploy-flag.
 *   3. A cron job (every minute) checks for the flag; if it exists, the
 *      cron removes the flag and runs deploy.sh. This avoids running
 *      shell commands from the PHP-FPM process and lets the deploy
 *      outlive GitHub's 10-second HTTP timeout naturally.
 *
 * cron line to add in cPanel → Cron Jobs (runs every minute, cheap):
 *   * * * * * [ -f ~/doctorato-website/storage/framework/.deploy-flag ] && rm ~/doctorato-website/storage/framework/.deploy-flag && bash ~/doctorato-website/deploy.sh >> ~/doctorato-website/storage/logs/deploy.log 2>&1
 */

$APP_DIR = dirname(__DIR__); // public/ is inside the app root
$FLAG = $APP_DIR . '/storage/framework/.deploy-flag';
$LOG = $APP_DIR . '/storage/logs/deploy-webhook.log';

/**
 * Read a value directly from .env — the PHP-FPM process that serves
 * this file doesn't necessarily have the .env variables in $_ENV /
 * getenv(), so we parse the file ourselves. Only looks at the first
 * matching line and strips quotes. Returns null if the key is absent.
 */
function readEnv(string $key, string $appDir): ?string {
    $file = $appDir . '/.env';
    if (!is_readable($file)) return null;
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = ltrim($line);
        if ($line === '' || $line[0] === '#') continue;
        $eq = strpos($line, '=');
        if ($eq === false) continue;
        $k = trim(substr($line, 0, $eq));
        if ($k !== $key) continue;
        $v = trim(substr($line, $eq + 1));
        // Strip surrounding single or double quotes if present.
        if (strlen($v) >= 2 && ($v[0] === '"' || $v[0] === "'") && substr($v, -1) === $v[0]) {
            $v = substr($v, 1, -1);
        }
        return $v;
    }
    return null;
}

$SECRET = readEnv('DEPLOY_WEBHOOK_SECRET', $APP_DIR)
    ?: getenv('DEPLOY_WEBHOOK_SECRET')
    ?: 'CHANGE_ME_TO_A_LONG_RANDOM_STRING';
$BRANCH = readEnv('DEPLOY_BRANCH', $APP_DIR) ?: (getenv('DEPLOY_BRANCH') ?: 'main');

function logline(string $msg, string $LOG): void {
    @file_put_contents($LOG, '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n", FILE_APPEND);
}

function reject(int $code, string $msg, string $LOG): void {
    http_response_code($code);
    logline("REJECTED ($code): $msg", $LOG);
    echo $msg;
    exit;
}

// 1. POST only
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    reject(405, 'POST only', $LOG);
}

// 2. HMAC signature — rejects any forged request
$payload = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (!$sigHeader || strncmp($sigHeader, 'sha256=', 7) !== 0) {
    reject(401, 'Missing signature', $LOG);
}
$expected = 'sha256=' . hash_hmac('sha256', $payload, $SECRET);
if (!hash_equals($expected, $sigHeader)) {
    reject(403, 'Bad signature', $LOG);
}

// 3. Branch filter
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
if ($event === 'ping') {
    http_response_code(200);
    logline('ping received', $LOG);
    echo 'pong';
    exit;
}
if ($event !== 'push') {
    reject(202, "Ignoring event: $event", $LOG);
}

$body = json_decode($payload, true);
$ref = $body['ref'] ?? '';
if ($ref !== "refs/heads/$BRANCH") {
    reject(202, "Ignoring push to $ref (only $BRANCH deploys)", $LOG);
}

// 4. Trigger the deploy immediately — no cron wait.
//    Strategy:
//      a) Try to spawn deploy.sh as a detached background process so
//         this request returns in milliseconds while deploy keeps
//         running (nohup ... > log 2>&1 &).
//      b) Always write a flag file too. If (a) is blocked by
//         disable_functions or a picky shared-hosting setup, the cron
//         picks up the flag within a minute — so we never lose a push.
$pusher = $body['pusher']['name'] ?? 'unknown';
$commit = substr($body['after'] ?? '', 0, 7);

// (a) Background spawn — runs in < 1 ms from the webhook's POV.
$deployedInline = false;
$deployScript = escapeshellarg($APP_DIR . '/deploy.sh');
$deployLog    = escapeshellarg($APP_DIR . '/storage/logs/deploy.log');
$cmd = "nohup bash $deployScript >> $deployLog 2>&1 < /dev/null &";

// exec may be in disable_functions on some shared hosts — check first.
$disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
if (function_exists('exec') && !in_array('exec', $disabled, true)) {
    @exec($cmd, $__out, $__rc);
    $deployedInline = ($__rc === 0);
}

// (b) Flag-file fallback — the cron (if configured) picks this up.
@mkdir(dirname($FLAG), 0775, true);
@file_put_contents($FLAG, json_encode([
    'at' => date('c'),
    'pusher' => $pusher,
    'commit' => $commit,
    'ref' => $ref,
    'inline_spawn' => $deployedInline,
]));

logline(($deployedInline ? 'spawned' : 'flagged') .
        " — push by $pusher @ $commit", $LOG);

http_response_code(202);
echo $deployedInline ? "Deploy started\n" : "Deploy queued (cron)\n";
