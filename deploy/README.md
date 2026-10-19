# Deploying

`deploy/deploy.sh` builds a release of HEAD on this machine, uploads it, and runs `deploy/remote-deploy.sh` on the server as the site user. Each release is unpacked into its own folder and `current` is switched to it in one rename, so a request never sees a half-deployed site.

## Deploy

1. Commit the work. A real deploy refuses a dirty working tree, since the checks run on the working tree but HEAD is what gets packed.
2. Run `deploy/deploy.sh --dry-run`. This runs Pint, Larastan, the asset build and Pest, builds the tarball in `deploy/build/`, checks it holds no secrets or test files and is under 5 MB, then stops before anything reaches the server.
3. Run `deploy/deploy.sh`. It repeats the dry run, uploads the tarball with `scp cyberpanel`, and pipes `remote-deploy.sh` to the server, which unpacks the release, links `.env` and `storage` from `app/shared`, runs `artisan optimize`, switches `current` and stops the PHP workers so they restart in the new release.

The server keeps three releases (the live one and the two newest of the rest), so there are always two to roll back to. A deploy that fails before the switch removes its own release and leaves the live site as it was.

## Server layout

`remote-deploy.sh` checks the parts of this it depends on and stops if they are wrong, but it does not create any of them. They are set up once, by root, before the first deploy.

```
/home/gengar.me/                  mode 711, so `nobody` can reach the site
├── app/                          owner genga7347, group nogroup, mode 2750 (setgid)
│   ├── releases/                 owner genga7347, group nogroup, mode 2750 (setgid)
│   │   └── <timestamp>-<hash>/   one folder per release
│   ├── current -> releases/<timestamp>-<hash>
│   └── shared/
│       ├── .env                  owner genga7347, mode 600
│       └── storage/              owner genga7347, with framework/cache, framework/views and logs
└── public_html -> app/current/public
```

LiteSpeed serves static files as `nobody`, which reads them through the `nogroup` group. The setgid bit on `app/releases` is what gives every unpacked file that group; without it the deploy would succeed and every image, stylesheet and font would return a HTTP `403` status, so the script fails if a release's `public` folder is not in `nogroup`.

PHP runs as `genga7347`, so it is the only user that needs to read `.env`. The script refuses to deploy if `.env` is not mode 600, and sets the cached config (`bootstrap/cache/config.php`, which holds `APP_KEY`) to mode 600 as well.

## First deploy

These only matter when the site is set up from scratch.

- The vhost (`/usr/local/lsws/conf/vhosts/gengar.me/vhost.conf`) must run PHP from `/usr/local/lsws/lsphp84/bin/lsphp`, the same PHP the deploy uses for `artisan`. CyberPanel created it with PHP 8.5.
- After `public_html` first becomes a link to `app/current/public`, OpenLiteSpeed needs a graceful restart (`/usr/local/lsws/bin/lswsctrl restart`) to read the release's `public/.htaccess`. Until then, every path other than `/` returns LiteSpeed's own `404` page. The restart reloads every site on the server, but lets requests in progress finish. Later deploys don't need it.
- CyberPanel's placeholder page is kept in `/home/gengar.me/public_html.placeholder-20261019`.

Every response carries `Cache-Control: no-transform`. Without it, Cloudflare's bot detection (a zone-wide setting) injects a script into each HTML page.

## Rollback

Rolling back points `current` at an earlier release that is still on the server. Run these as `genga7347` (from root, `sudo -u genga7347 -H bash`).

1. List the releases and see which one is live.

   ```
   cd /home/gengar.me/app
   ls releases
   readlink current
   ```

2. Switch `current` to the release before it in one rename, so a request never sees a missing link.

   ```
   ln -sfn /home/gengar.me/app/releases/<previous release> current.next && mv -Tf current.next current
   ```

3. Stop the PHP workers, so none keep serving the release that was just replaced. LiteSpeed starts them again in the release `current` now points to.

   ```
   pkill -u genga7347 lsphp
   ```
