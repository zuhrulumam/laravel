#!/bin/sh
set -e

echo "Starting Laravel application..."

# Create SQLite database if using SQLite
if [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Creating SQLite database..."
    mkdir -p /app/database
    touch /app/database/database.sqlite
    chown -R www-data:www-data /app/database
fi

# Clear any old cached config
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache config with runtime ENV variables
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
chown -R www-data:www-data storage bootstrap/cache

echo "Laravel application ready!"

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf