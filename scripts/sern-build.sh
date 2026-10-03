#!/usr/bin/env bash
set -euo pipefail
# Build an immutable application release on the workstation or CI, never on Sern.
repo=$(git rev-parse --show-toplevel)
version=$(git rev-parse HEAD)
output=${1:?Supply an output directory outside the repository}
mkdir -p "$output"
output=$(cd "$output" && pwd)
work=$(mktemp -d)
trap 'rm -rf -- "$work"' EXIT
mkdir "$work/app"
git -C "$repo" archive HEAD | tar -xf - -C "$work/app"
cd "$work/app"
# The lockfile pins Composer dependencies. Do not run migrations or bootstrap users.
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts
APP_ENV=production php artisan package:discover --ansi
APP_ENV=production php artisan filament:assets --ansi
npm ci --allow-remote=all
npm run build
printf '%s\n' "$version" > RELEASE
# Writable application state lives in shared storage on Sern.
rm -rf node_modules tests .github storage
rm -f public/hot .env
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs storage/app/{private,public}
tar -czf "$output/familio-linux-amd64.tar.gz" app
cd "$output"
sha256sum familio-linux-amd64.tar.gz > SHA256SUMS
printf 'Built Familio %s in %s\n' "$version" "$output"
