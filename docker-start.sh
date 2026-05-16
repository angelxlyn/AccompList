#!/bin/bash
# docker-start.sh
# Runs on every container start on Render.
# Handles the dynamic $PORT Render injects at runtime.

set -e

# ── 1. Use Render's PORT or fall back to 80 ───────────────────────────────────
APP_PORT="${PORT:-80}"

# Update Apache to listen on the correct port
sed -i "s/Listen 80/Listen ${APP_PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${APP_PORT}>/" /etc/apache2/sites-available/*.conf

echo "==> Apache will listen on port ${APP_PORT}"

# ── 2. Cache Laravel config/routes/views for production ──────────────────────
echo "==> Caching Laravel config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 3. Run migrations (--force skips the production prompt) ──────────────────
echo "==> Running migrations..."
php artisan migrate --force

echo "==> Starting Apache..."
exec apache2-foreground