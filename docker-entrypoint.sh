#!/bin/sh
set -e

# Cache configuration, routes, and views for production performance
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run database migrations (SQLite or Postgres)
php artisan migrate --force || true

# Start Nginx and PHP-FPM via supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
