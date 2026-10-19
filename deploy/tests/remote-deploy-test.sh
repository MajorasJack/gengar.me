#!/usr/bin/env bash
# Runs deploy/remote-deploy.sh against a throwaway site folder with a fake PHP. Needs GNU
# coreutils for `mv -T`, so it runs on Linux (CI does) or, on macOS, in Docker:
#   docker run --rm -v "$PWD":/src -w /src debian:stable-slim deploy/tests/remote-deploy-test.sh
set -euo pipefail

SCRIPT="$(cd "$(dirname "$0")/.." && pwd)/remote-deploy.sh"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

export SITE_HOME="$WORK/site" SITE_USER="$(id -un)" PHP_BIN="$WORK/php"
APP="$SITE_HOME/app"

# The fake PHP fails while $WORK/fail exists, and otherwise logs its arguments.
cat > "$PHP_BIN" <<'EOF'
#!/usr/bin/env bash
[ ! -e "$(dirname "$0")/fail" ] || exit 1
echo "$*" >> "$(dirname "$0")/php.log"
EOF
chmod +x "$PHP_BIN"

mkdir -p "$APP/releases" "$APP/shared/storage" "$WORK/build/public"
touch "$APP/shared/.env"
echo ok > "$WORK/build/public/index.php"

fail() {
    echo "FAIL: $*" >&2
    exit 1
}

deploy() {
    tar -czf "$APP/releases/$1.tgz" -C "$WORK/build" .
    bash "$SCRIPT" "$1"
}

deploy 20261019000001-aaaaaaa
[ "$(readlink "$APP/current")" = "$APP/releases/20261019000001-aaaaaaa" ] || fail "a good deploy did not switch current"
[ -f "$APP/current/public/index.php" ] || fail "the release was not unpacked"
[ "$(readlink "$APP/current/.env")" = "$APP/shared/.env" ] || fail ".env is not linked to shared"
[ "$(readlink "$APP/current/storage")" = "$APP/shared/storage" ] || fail "storage is not linked to shared"
[ ! -e "$APP/releases/20261019000001-aaaaaaa.tgz" ] || fail "the tarball was not removed"
[ "$(cat "$WORK/php.log")" = "artisan optimize" ] || fail "artisan optimize did not run"
echo "ok: a good deploy switches current"

touch "$WORK/fail"
if deploy 20261019000002-bbbbbbb; then
    fail "a failing artisan step did not fail the deploy"
fi
rm "$WORK/fail"
[ "$(readlink "$APP/current")" = "$APP/releases/20261019000001-aaaaaaa" ] || fail "a failed deploy moved current"
[ ! -e "$APP/releases/20261019000002-bbbbbbb" ] || fail "a failed deploy left its release behind"
echo "ok: a failing artisan step leaves current unchanged"

for release in 20261019000003-ccccccc 20261019000004-ddddddd 20261019000005-eeeeeee; do
    deploy "$release"
done
[ "$(ls "$APP/releases" | tr '\n' ' ')" = "20261019000003-ccccccc 20261019000004-ddddddd 20261019000005-eeeeeee " ] \
    || fail "expected the three newest releases, got: $(ls "$APP/releases" | tr '\n' ' ')"
[ "$(readlink "$APP/current")" = "$APP/releases/20261019000005-eeeeeee" ] || fail "current is not the newest release"
echo "ok: three releases are kept"

if SITE_USER="not-$SITE_USER" deploy 20261019000006-fffffff; then
    fail "the script ran as the wrong user"
fi
echo "ok: the script refuses to run as anyone but the site user"

if deploy '../../site'; then
    fail "the script accepted a release name outside the expected shape"
fi
echo "ok: the script refuses an unexpected release name"

echo "All remote deploy checks passed"
