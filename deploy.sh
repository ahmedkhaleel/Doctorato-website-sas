#!/usr/bin/env bash
#
# Manual / webhook-triggered deploy for cPanel shared hosting.
#
# Run by hand:     bash ~/public_html/deploy.sh
# Run from cron:   * * * * * [ -f ~/public_html/storage/framework/.deploy-flag ] && rm ~/public_html/storage/framework/.deploy-flag && bash ~/public_html/deploy.sh >> ~/public_html/storage/logs/deploy.log 2>&1
# Run from webhook (see public/deploy.php).
#
# Expects:
#   - Git remote `origin` already set to the GitHub repo.
#   - .env, storage/, and public/build/ already live on the server.
#
# Exits non-zero on any step's failure so callers see a meaningful status.

set -euo pipefail

# Default APP_DIR to the script's own directory — no more guessing based
# on conventional paths. Caller can still override with `APP_DIR=... bash
# deploy.sh`. Works regardless of whether the Laravel app lives at
# ~/public_html, ~/doctorato-website, or anywhere else.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="${APP_DIR:-$SCRIPT_DIR}"
BRANCH="${DEPLOY_BRANCH:-main}"
LOG="$APP_DIR/storage/logs/deploy.log"

# Some cPanel PHP builds disable fileinfo/iconv. Composer has a flag to
# skip those platform checks so install doesn't abort. Admin should
# enable them via cPanel → Select PHP Version for real — this is a
# pragmatic fallback.
COMPOSER_EXTRA_FLAGS="${COMPOSER_EXTRA_FLAGS:---ignore-platform-req=ext-fileinfo --ignore-platform-req=ext-iconv}"

# Ensure log dir exists the very first time.
mkdir -p "$(dirname "$LOG")"

log() {
    echo "[$(date '+%F %T')] $1" | tee -a "$LOG"
}

cd "$APP_DIR" || { echo "APP_DIR not found: $APP_DIR"; exit 1; }

log "── deploy start ──"
log "pulling latest from origin/$BRANCH"
git fetch --prune origin
git reset --hard "origin/$BRANCH"

# PHP binary — prefer the cPanel ea-php84 build if present, since most
# shared hosts ship an older `/usr/local/bin/php` (8.3 or lower) on the
# CLI even when the account is configured for 8.4 on the web. Laravel 13
# needs 8.4, so this matters. Override with `PHP_BIN=... bash deploy.sh`.
PHP_BIN="${PHP_BIN:-}"
if [ -z "$PHP_BIN" ]; then
    for candidate in \
        /opt/cpanel/ea-php84/root/usr/bin/php \
        /opt/cpanel/ea-php83/root/usr/bin/php \
        /opt/cpanel/ea-php82/root/usr/bin/php \
        /usr/local/bin/php \
        "$(command -v php 2>/dev/null)"; do
        if [ -x "$candidate" ]; then
            PHP_BIN="$candidate"
            break
        fi
    done
fi
if [ -z "$PHP_BIN" ]; then
    log "no PHP binary found; aborting"
    exit 1
fi
log "using php: $PHP_BIN ($($PHP_BIN --version 2>&1 | head -1))"

# Composer — probe the usual cPanel spots, then fall back to the
# composer.phar in the repo root. Runs with whatever $PHP_BIN we chose
# above so Composer and the app agree on the PHP version.
COMPOSER_CMD=""
if command -v composer >/dev/null 2>&1; then
    COMPOSER_CMD="composer"
elif [ -x /opt/cpanel/composer/bin/composer ]; then
    COMPOSER_CMD="/opt/cpanel/composer/bin/composer"
elif [ -x /usr/local/bin/composer ]; then
    COMPOSER_CMD="/usr/local/bin/composer"
elif [ -f "$APP_DIR/composer.phar" ]; then
    COMPOSER_CMD="$PHP_BIN $APP_DIR/composer.phar"
else
    log "composer not found (no 'composer' binary or composer.phar); aborting"
    exit 1
fi
log "using composer: $COMPOSER_CMD"

log "installing production PHP deps"
$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction $COMPOSER_EXTRA_FLAGS

log "entering maintenance mode"
$PHP_BIN artisan down || true

log "running migrations"
$PHP_BIN artisan migrate --force

log "ensuring storage symlink"
$PHP_BIN artisan storage:link || true

log "flushing + rebuilding caches"
$PHP_BIN artisan cache:clear
$PHP_BIN artisan config:clear
$PHP_BIN artisan route:clear
$PHP_BIN artisan view:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

log "leaving maintenance mode"
$PHP_BIN artisan up

log "── deploy done ──"
