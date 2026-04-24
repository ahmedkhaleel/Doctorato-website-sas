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

# Some hosts ship composer as `composer`, some as `composer.phar`, some at
# /usr/local/bin/composer. Try whichever is available.
COMPOSER="$(command -v composer || echo /usr/local/bin/composer)"
if [ ! -x "$COMPOSER" ] && [ ! -f "$COMPOSER" ]; then
    log "composer not found on PATH; aborting"
    exit 1
fi

log "installing production PHP deps"
"$COMPOSER" install --no-dev --optimize-autoloader --no-interaction

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
