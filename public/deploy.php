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

$SECRET = getenv('DEPLOY_WEBHOOK_SECRET') ?: 'CHANGE_ME_TO_A_LONG_RANDOM_STRING';
$BRANCH = getenv('DEPLOY_BRANCH') ?: 'main';
$APP_DIR = dirname(__DIR__); // public/ is inside the app root
$FLAG = $APP_DIR . '/storage/framework/.deploy-flag';
$LOG = $APP_DIR . '/storage/logs/deploy-webhook.log';

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

// 4. Drop a flag file; the cron picks it up on the next tick and runs
//    deploy.sh. No shell exec from the web request — the trigger is just
//    a filesystem write, which is safe on shared hosting and avoids
//    PHP-FPM timeouts for long deploys.
@mkdir(dirname($FLAG), 0775, true);
$written = @file_put_contents($FLAG, json_encode([
    'at' => date('c'),
    'pusher' => $body['pusher']['name'] ?? null,
    'commit' => substr($body['after'] ?? '', 0, 7),
    'ref' => $ref,
]));

if ($written === false) {
    reject(500, 'Could not write deploy flag', $LOG);
}

logline("flag written — push by " . ($body['pusher']['name'] ?? 'unknown') .
        " @ " . substr($body['after'] ?? '', 0, 7), $LOG);

http_response_code(202);
echo "Deploy queued\n";
