#!/bin/sh
set -e

echo "Running migrations..."
php artisan migrate --force || echo "WARNING: Migration failed, continuing anyway..."

echo "Caching config..."
php artisan config:cache || echo "WARNING: config cache failed"

echo "Caching routes..."
php artisan route:cache || echo "WARNING: route cache failed"

echo "Caching views..."
php artisan view:cache || echo "WARNING: view cache failed"

echo "Starting PHP-FPM..."
php-fpm --daemonize

echo "Starting Nginx..."
nginx -g 'daemon off;'
