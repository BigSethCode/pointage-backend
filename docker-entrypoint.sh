#!/bin/sh
set -e

echo "Running migrations..."
php artisan migrate --force || echo "WARNING: Migration failed, continuing anyway..."

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache

echo "Starting PHP-FPM..."
exec php-fpm
