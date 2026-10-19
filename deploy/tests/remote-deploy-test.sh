#!/usr/bin/env bash
# Runs deploy/remote-deploy.sh against a throwaway site folder with a fake PHP. Needs GNU
# coreutils and findutils (`mv -T`, `stat -c`, `find -regextype`), so it runs on Linux (CI does)
# or, on macOS, in Docker:
#   docker run --rm -v "$PWD":/src -w /src debian:stable-slim deploy/tests/remote-deploy-test.sh
set -euo pipefail

SCRIPT="$(cd "$(dirname "$0")/.." && pwd)/remote-deploy.sh"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

# SITE_HOME is a symlink, as a home folder can be on the server, so the script has to compare
# releases by what they resolve to.
mkdir -p "$WORK/real"
ln -s "$WORK/real" "$WORK/site"
export SITE_HOME="$WORK/site" SITE_USER="$(id -un)" PHP_BIN="$WORK/php" WEB_GROUP="$(id -gn)"
APP="$SITE_HOME/app"

# The script arrives on stdin, as it does over ssh, and these stand-ins drain stdin the way a
# careless command would, so a missing </dev/null cuts the script short. The fake PHP fails while
# $WORK/fail exists, and otherwise writes the config cache and logs its arguments.
mkdir "$WORK/bin"
for tool in tar find; do
    printf '#!/usr/bin/env bash\ncat > /dev/null\nexec %s "$@"\n' "$(command -v "$tool")" > "$WORK/bin/$tool"
done
cat > "$WORK/bin/pkill" <<EOF
#!/usr/bin/env bash
cat > /dev/null
echo "\$*" >> "$WORK/pkill.log"
EOF
cat > "$PHP_BIN" <<'EOF'
#!/usr/bin/env bash
cat > /dev/null
[ ! -e "$(dirname "$0")/fail" ] || exit 1
mkdir -p bootstrap/cache
echo '<?php return [];' > bootstrap/cache/config.php
echo "$*" >> "$(dirname "$0")/php.log"
EOF
chmod +x "$PHP_BIN" "$WORK/bin/"*

mkdir -p "$APP/releases" "$APP/shared/storage" "$WORK/build/public"
touch "$APP/shared/.env"
chmod 600 "$APP/shared/.env"
echo ok > "$WORK/build/public/index.php"

fail() {
    echo "FAIL: $*" >&2
    exit 1
}

remote() {
    cat "$SCRIPT" | PATH="$WORK/bin:$PATH" bash -s -- "$1" | tee "$WORK/out"
}

deploy() {
    tar -czf "$APP/releases/$1.tgz" -C "$WORK/build" .
    remote "$1"
}

releases() {
    ls "$APP/releases" | tr '\n' ' '
}

deploy 20261019000001-aaaaaaa
[ "$(readlink "$APP/current")" = "$APP/releases/20261019000001-aaaaaaa" ] || fail "a good deploy did not switch current"
[ -f "$APP/current/public/index.php" ] || fail "the release was not unpacked"
[ "$(readlink "$APP/current/.env")" = "$APP/shared/.env" ] || fail ".env is not linked to shared"
[ "$(readlink "$APP/current/storage")" = "$APP/shared/storage" ] || fail "storage is not linked to shared"
[ ! -e "$APP/releases/20261019000001-aaaaaaa.tgz" ] || fail "the tarball was not removed"
[ "$(cat "$WORK/php.log")" = "artisan optimize" ] || fail "artisan optimize did not run"
[ "$(cat "$WORK/pkill.log")" = "-u $SITE_USER lsphp" ] || fail "the PHP workers were not stopped"
[ "$(tail -n 1 "$WORK/out")" = "Deployed 20261019000001-aaaaaaa" ] || fail "a command read the rest of the script from stdin"
echo "ok: a good deploy switches current"

[ "$(stat -c %a "$APP/current/bootstrap/cache/config.php")" = 600 ] || fail "the config cache is readable by the web server"
echo "ok: the config cache is readable only by the site user"

touch "$APP/current/marker"
deploy 20261019000001-aaaaaaa
[ -e "$APP/current/marker" ] || fail "redeploying the live release replaced it"
[ "$(tail -n 1 "$WORK/out")" = "Release 20261019000001-aaaaaaa is already live" ] || fail "the live release was not recognised"
echo "ok: redeploying the live release behind a symlinked home leaves it alone"

touch "$WORK/fail"
if deploy 20261019000002-bbbbbbb; then
    fail "a failing artisan step did not fail the deploy"
fi
rm "$WORK/fail"
[ "$(readlink "$APP/current")" = "$APP/releases/20261019000001-aaaaaaa" ] || fail "a failed deploy moved current"
[ ! -e "$APP/releases/20261019000002-bbbbbbb" ] || fail "a failed deploy left its release behind"
echo "ok: a failing artisan step leaves current unchanged"

echo 'not a tarball' > "$APP/releases/20261019000002-bbbbbbb.tgz"
if remote 20261019000002-bbbbbbb; then
    fail "a corrupt tarball did not fail the deploy"
fi
[ ! -e "$APP/releases/20261019000002-bbbbbbb" ] || fail "a failed unpack left its release behind"
[ ! -e "$APP/releases/20261019000002-bbbbbbb.tgz" ] || fail "a failed unpack left its tarball behind"
echo "ok: a failed unpack removes the release and its tarball"

if WEB_GROUP="not-$WEB_GROUP" deploy 20261019000002-bbbbbbb; then
    fail "a release without the web server's group was deployed"
fi
[ "$(readlink "$APP/current")" = "$APP/releases/20261019000001-aaaaaaa" ] || fail "a release without the web server's group went live"
[ ! -e "$APP/releases/20261019000002-bbbbbbb" ] || fail "a release without the web server's group was left behind"
echo "ok: the script refuses a release the web server cannot read"

chmod 644 "$APP/shared/.env"
if deploy 20261019000002-bbbbbbb; then
    fail "the script deployed with a .env other users can read"
fi
chmod 600 "$APP/shared/.env"
rm "$APP/releases/20261019000002-bbbbbbb.tgz"
[ ! -e "$APP/releases/20261019000002-bbbbbbb" ] || fail "a .env other users can read did not stop the deploy early"
echo "ok: the script refuses a .env other users can read"

mkdir "$APP/releases/backup"
for release in 20261019000003-ccccccc 20261019000004-ddddddd 20261019000005-eeeeeee; do
    deploy "$release"
done
[ "$(releases)" = "20261019000003-ccccccc 20261019000004-ddddddd 20261019000005-eeeeeee backup " ] \
    || fail "expected the three newest releases and the stray folder, got: $(releases)"
[ "$(readlink "$APP/current")" = "$APP/releases/20261019000005-eeeeeee" ] || fail "current is not the newest release"
echo "ok: three releases are kept and a stray folder is left alone"

if remote 20261019000003-ccccccc; then
    fail "the script ran without a tarball"
fi
[ -f "$APP/releases/20261019000003-ccccccc/public/index.php" ] || fail "a rerun without a tarball removed an older release"
echo "ok: a rerun without a tarball leaves an older release alone"

deploy 20261018000000-fffffff
[ "$(readlink "$APP/current")" = "$APP/releases/20261018000000-fffffff" ] || fail "an older-named release did not go live"
[ "$(releases)" = "20261018000000-fffffff 20261019000004-ddddddd 20261019000005-eeeeeee backup " ] \
    || fail "expected the live release and the two newest, got: $(releases)"
echo "ok: pruning keeps the live release when its name is the oldest"

if SITE_USER="not-$SITE_USER" deploy 20261019000007-ababab7; then
    fail "the script ran as the wrong user"
fi
echo "ok: the script refuses to run as anyone but the site user"

if deploy '../../site'; then
    fail "the script accepted a release name outside the expected shape"
fi
echo "ok: the script refuses an unexpected release name"

echo "All remote deploy checks passed"
