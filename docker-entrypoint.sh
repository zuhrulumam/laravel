#!/bin/bash
set -e

# Semua caching di-run saat startup
# supaya env vars dari EasyPanel terbaca (APP_KEY, DB_HOST, dll)
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"