#!/bin/sh
set -eu

key_file=/var/www/runtime/app_key

if [ ! -s "$key_file" ]; then
    php artisan key:generate --show > "$key_file"
fi

export APP_KEY="$(cat "$key_file")"
exec "$@"
