#!/usr/bin/env bash
# render-build.sh — runs during Render's build phase

set -e  # exit immediately if any command fails

echo "==> Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Setting storage permissions..."
chmod -R 775 storage bootstrap/cache

echo "==> Build complete."