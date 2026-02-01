#!/bin/bash
set -e

# ─── Wait for MySQL ───────────────────────────
# config:cache/route:cache/view:cache nggak butuh DB
# tapi first request butuh DB — kalau MySQL belum ready,
# request hang dan EasyPanel kill container-nya
if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "Waiting for database ($DB_HOST:${DB_PORT:-3306})..."
    until bash -c "echo >/dev/tcp/$DB_HOST/${DB_PORT:-3306}" 2>/dev/null; do
        echo "DB not ready, retrying in 2s..."
        sleep 2
    done
    echo "Database is ready."
fi

# ─── Laravel caching ──────────────────────────
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"