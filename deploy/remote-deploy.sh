#!/usr/bin/env bash
# Unpacks a release uploaded by deploy/deploy.sh and switches the site over to it. Adapted from
# Larder's remote-deploy.sh. Runs on the server as the site user, piped in over ssh.
# SITE_HOME, SITE_USER, PHP_BIN and WEB_GROUP default to the server's values;
# deploy/tests/remote-deploy-test.sh overrides them to run the script against a throwaway folder.
# The script itself arrives on stdin (`bash -s`), so every command that could read stdin gets
# </dev/null, or it would swallow the rest of the script.
# Usage: remote-deploy.sh <release>
set -euo pipefail

RELEASE="${1:-}"
SITE_HOME="${SITE_HOME:-/home/gengar.me}"
SITE_USER="${SITE_USER:-genga7347}"
PHP_BIN="${PHP_BIN:-/usr/local/lsws/lsphp84/bin/php}"
WEB_GROUP="${WEB_GROUP:-nogroup}"
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

# The release folders are readable by the web server's group, so .env must not be. This fails
# rather than fixing the mode, since a readable .env may already have leaked APP_KEY.
if [ "$(stat -L -c %a "$APP/shared/.env")" != 600 ]; then
    echo "$APP/shared/.env must have mode 600, not $(stat -L -c %a "$APP/shared/.env")" >&2
    exit 1
fi

# Redeploying the live release must not unpack over the files it is serving. -ef compares the
# files themselves, so a symlinked SITE_HOME cannot hide a match.
if [ "$APP/current" -ef "$DIR" ]; then
    rm -f "$APP/releases/$RELEASE.tgz"
    echo "Release $RELEASE is already live"
    exit 0
fi

# Checked before an existing folder is removed, so a rerun without an upload cannot delete an
# older release it is unable to rebuild.
if [ ! -f "$APP/releases/$RELEASE.tgz" ]; then
    echo "Missing $APP/releases/$RELEASE.tgz" >&2
    exit 1
fi

# A folder left by an earlier deploy of the same release is replaced, not unpacked over.
if [ -d "$DIR" ]; then
    rm -r -- "$DIR"
fi

# Until current is switched, a failure removes the half-built release and its tarball, so it
# never takes one of the kept rollback slots. The live release is never removed.
trap '[ "$APP/current" -ef "$DIR" ] || rm -rf -- "$DIR" "$APP/releases/$RELEASE.tgz"' EXIT

mkdir -p "$DIR"
tar -xzf "$APP/releases/$RELEASE.tgz" -C "$DIR" </dev/null
rm "$APP/releases/$RELEASE.tgz"

# LiteSpeed serves static files as `nobody`, which reads through the `nogroup` group that new
# files inherit from the setgid release folders.
find "$DIR" -type d -exec chmod 2750 {} + </dev/null
find "$DIR" -type f -exec chmod g+r {} + </dev/null

# Without the setgid group the deploy would succeed and every static file would return a 403.
if [ "$(stat -c %G "$DIR/public")" != "$WEB_GROUP" ]; then
    echo "$DIR/public has group $(stat -c %G "$DIR/public"), not $WEB_GROUP; is $APP/releases setgid $WEB_GROUP?" >&2
    exit 1
fi

ln -sfn "$APP/shared/.env" "$DIR/.env"
ln -sfn "$APP/shared/storage" "$DIR/storage"

cd "$DIR"
# Caches config, events, routes and views.
"$PHP_BIN" artisan optimize </dev/null
# The cached config holds APP_KEY and every other secret from .env.
chmod 600 bootstrap/cache/config.php

# One rename, so a request never sees a half-switched release. mv -T is GNU only.
ln -sfn "$DIR" "$APP/current.next"
mv -Tf "$APP/current.next" "$APP/current"; trap - EXIT

# Running PHP processes keep the old release's paths in their caches; they respawn on the next
# request.
pkill -u "$SITE_USER" lsphp </dev/null || true

# Release names start with a UTC timestamp, so name order is deploy order. Only folders named
# like a release are counted, and the live one (older than the rest after a rollback) is always
# kept, with the newest of the others up to KEEP_RELEASES in total.
find "$APP/releases" -mindepth 1 -maxdepth 1 -type d -regextype posix-extended \
    -regex '.*/[0-9]{14}-[0-9a-f]{7,40}' ! -name "$(basename "$(readlink -f "$APP/current")")" </dev/null \
    | sort -r | tail -n +"$KEEP_RELEASES" | xargs -r -d '\n' rm -r --

echo "Deployed $RELEASE"
