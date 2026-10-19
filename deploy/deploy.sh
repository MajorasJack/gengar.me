#!/usr/bin/env bash
# Checks, builds and uploads a release of HEAD, then runs deploy/remote-deploy.sh on the server.
# It packs from `git archive`, so only committed files are shipped, never .env or uncommitted work.
# Usage: deploy/deploy.sh [--dry-run]
#   --dry-run  build and check the tarball, then stop before anything touches the server
set -euo pipefail

DRY_RUN=false
if [ "${1:-}" = "--dry-run" ]; then
    DRY_RUN=true
elif [ -n "${1:-}" ]; then
    echo "Unknown option: $1" >&2
    exit 1
fi

HOST=cyberpanel
SITE_HOME=/home/gengar.me
SITE_USER=genga7347
MAX_BYTES=$((5 * 1024 * 1024))
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
RELEASE="$(date -u +%Y%m%d%H%M%S)-$(git -C "$ROOT" rev-parse --short HEAD)"
OUT="$ROOT/deploy/build/$RELEASE.tgz"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

# The checks run against the working tree, so it has to match what gets packed.
if [ "$DRY_RUN" = false ] && [ -n "$(git -C "$ROOT" status --porcelain)" ]; then
    echo "The working tree has uncommitted changes; commit or stash them first" >&2
    exit 1
fi

cd "$ROOT"
vendor/bin/pint --test
vendor/bin/phpstan analyse --no-progress --memory-limit=512M
npm run build
vendor/bin/pest

git archive HEAD | tar -x -C "$WORK"
cd "$WORK"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress
npm ci --no-audit --no-fund
npm run build

rm -rf node_modules tests docs deploy .github .env.example phpunit.xml phpstan.neon pint.json
# Package docs are about a tenth of the tarball, which otherwise sits right on the 5 MB limit.
find vendor -type f -name '*.md' -delete
find vendor -depth -type d -name docs -exec rm -r {} +

# Boot the stripped release without dev dependencies, so a missing class fails here rather than
# on the server. The server builds its own caches, so these are cleared again.
export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)" APP_ENV=production
php artisan route:list
php artisan config:cache
php artisan view:cache
php artisan config:clear
php artisan view:clear
unset APP_KEY APP_ENV

# storage is linked from app/shared on the server, so the release must not carry its own.
rm -rf storage

mkdir -p "$ROOT/deploy/build"
# Without these, macOS tar adds AppleDouble files and xattr headers that GNU tar warns about.
COPYFILE_DISABLE=1 tar --no-mac-metadata --no-xattrs -cf - . | gzip -9 > "$OUT"

# A last check on the tarball itself, so a bad build never reaches the server.
FORBIDDEN="$(tar -tzf "$OUT" | grep -E '(^|/)\.env(\..+)?$|^(\./)?(tests|storage)/|(^|/)node_modules/|\.pem$' || true)"
if [ -n "$FORBIDDEN" ]; then
    echo "The tarball contains files that must not be deployed:" >&2
    echo "$FORBIDDEN" >&2
    rm "$OUT"
    exit 1
fi

SIZE="$(wc -c < "$OUT" | tr -d ' ')"
if [ "$SIZE" -gt "$MAX_BYTES" ]; then
    echo "The tarball is $SIZE bytes, over the $MAX_BYTES byte limit" >&2
    rm "$OUT"
    exit 1
fi

echo "Built $OUT ($SIZE bytes)"

if [ "$DRY_RUN" = true ]; then
    echo "Dry run: nothing was uploaded"
    exit 0
fi

test -f "$ROOT/deploy/remote-deploy.sh"
scp "$OUT" "$HOST:$SITE_HOME/app/releases/$RELEASE.tgz"
# `ssh cyberpanel` logs in as root, so the upload is handed to the site user, which runs the
# rest. The script is piped in, so the server always runs the version from this commit.
ssh "$HOST" "chown $SITE_USER $SITE_HOME/app/releases/$RELEASE.tgz && sudo -u $SITE_USER -H bash -s -- $RELEASE" \
    < "$ROOT/deploy/remote-deploy.sh"
rm "$OUT"

echo "Deployed $RELEASE"
