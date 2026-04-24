#!/usr/bin/env bash
#
# Manual / webhook-triggered deploy for cPanel shared hosting.
#
# Run by hand:     bash ~/doctorato-website/deploy.sh
# Run from cron:   * * * * * test -f /tmp/doctorato-deploy.flag && rm /tmp/doctorato-deploy.flag && bash ~/doctorato-website/deploy.sh >> ~/doctorato-website/storage/logs/deploy.log 2>&1
# Run from webhook (see public/deploy.php).
#
# Expects:
#   - Git remote `origin` already set to the GitHub repo.
#   - .env, storage/, and public/build/ already live on the server.
#   - composer available on $PATH (fallback: /usr/local/bin/composer).
#
# Exits non-zero on any step's failure so callers see a meaningful status.

set -euo pipefail

APP_DIR="${APP_DIR:-$HOME/doctorato-website}"
BRANCH="${DEPLOY_BRANCH:-main}"
LOG="$APP_DIR/storage/logs/deploy.log"

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

# cPanel shared hosts ship composer in many places. Probe for:
#   1. `composer` on PATH
#   2. /opt/cpanel/composer/bin/composer (cPanel's bundled copy)
#   3. /usr/local/bin/composer
#   4. A composer.phar shipped in the repo root
# Fall back to running the .phar with php directly if nothing else exists.
PHP_BIN="$(command -v php || echo /usr/local/bin/php)"
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
$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction

log "entering maintenance mode"
php artisan down || true

log "running migrations"
php artisan migrate --force

log "ensuring storage symlink"
php artisan storage:link || true

log "flushing + rebuilding caches"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

log "leaving maintenance mode"
php artisan up

log "── deploy done ──"
