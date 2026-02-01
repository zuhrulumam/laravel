#!/bin/bash
set -e

# config:cache di-run saat startup, bukan build time
# supaya env vars dari EasyPanel / docker-compose terbaca
php artisan config:cache

# Uncomment kalau butuh auto-migrate:
# php artisan migrate --force

exec "$@"