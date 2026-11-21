#!/usr/bin/env bash
# Menyiapkan PINTAR di GitHub Codespaces memakai SQLite (tanpa MySQL).
set -e
composer install --no-interaction --prefer-dist
if [ ! -f .env ]; then
  cp .env.example .env
  sed -i 's/^DB_CONNECTION=mysql/DB_CONNECTION=sqlite/; s/^DB_\(HOST\|PORT\|DATABASE\|USERNAME\|PASSWORD\)=/# DB_\1=/' .env
  php artisan key:generate --no-interaction
fi
touch database/database.sqlite
php artisan migrate --seed --force --no-interaction
php artisan storage:link --no-interaction || true
echo "PINTAR siap. Buka tab PORTS lalu klik alamat port 8000."
