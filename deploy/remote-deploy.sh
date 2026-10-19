#!/usr/bin/env bash
# Unpacks a release uploaded by deploy/deploy.sh and switches the site over to it. Adapted from
# Larder's remote-deploy.sh. Runs on the server as the site user, piped in over ssh.
# SITE_HOME, SITE_USER and PHP_BIN default to the server's values; deploy/tests/remote-deploy-test.sh
# overrides them to run the script against a throwaway folder.
# Usage: remote-deploy.sh <release>
set -euo pipefail

RELEASE="${1:-}"
SITE_HOME="${SITE_HOME:-/home/gengar.me}"
SITE_USER="${SITE_USER:-genga7347}"
PHP_BIN="${PHP_BIN:-/usr/local/lsws/lsphp84/bin/php}"
APP="$SITE_HOME/app"
DIR="$APP/releases/$RELEASE"
KEEP_RELEASES=3

# The name ends up in rm paths, so only the shape deploy.sh produces is accepted.
if [[ ! "$RELEASE" =~ ^[0-9]{14}-[0-9a-f]{7,40}$ ]]; then
    echo "Expected a release named <UTC timestamp>-<short hash>, got '$RELEASE'" >&2
    exit 1
fi

# Files written as anyone else could not be read or cleaned up by the site user later.
if [ "$(id -un)" != "$SITE_USER" ]; then
    echo "Run this as $SITE_USER, not $(id -un)" >&2
    exit 1
fi

# sudo keeps the caller's working directory, which the site user may not be able to read.
cd "$APP"

if [ ! -f "$APP/shared/.env" ]; then
    echo "Missing $APP/shared/.env" >&2
    exit 1
fi

# Redeploying the live release must not unpack over the files it is serving.
if [ "$(readlink -f "$APP/current" 2>/dev/null)" = "$DIR" ]; then
    rm -f "$APP/releases/$RELEASE.tgz"
    echo "Release $RELEASE is already live"
    exit 0
fi

# A folder left by an earlier failed deploy of the same release is replaced, not unpacked over.
if [ -d "$DIR" ]; then
    rm -r -- "$DIR"
fi

# Until current is switched, a failure removes the half-built release, so it never takes one of
# the kept rollback slots.
trap 'rm -rf -- "$DIR"' EXIT

mkdir -p "$DIR"
tar -xzf "$APP/releases/$RELEASE.tgz" -C "$DIR"
rm "$APP/releases/$RELEASE.tgz"

# LiteSpeed serves static files as `nobody`, which reads through the `nogroup` group that new
# files inherit from the setgid release folders.
find "$DIR" -type d -exec chmod 2750 {} +
find "$DIR" -type f -exec chmod g+r {} +

ln -sfn "$APP/shared/.env" "$DIR/.env"
ln -sfn "$APP/shared/storage" "$DIR/storage"

cd "$DIR"
# Caches config, events, routes and views.
"$PHP_BIN" artisan optimize

# One rename, so a request never sees a half-switched release. mv -T is GNU only.
ln -sfn "$DIR" "$APP/current.next"
mv -Tf "$APP/current.next" "$APP/current"
trap - EXIT

# Running PHP processes keep the old release's paths in their caches; they respawn on the next
# request.
pkill -u "$SITE_USER" lsphp || true

# Release names start with a UTC timestamp, so name order is deploy order.
printf '%s\n' "$APP"/releases/*/ | sort -r | tail -n +$((KEEP_RELEASES + 1)) | xargs -r rm -r --

echo "Deployed $RELEASE"
