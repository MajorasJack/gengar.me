# ADR-002: Tarball release deploys to the existing CyberPanel server

Status: Accepted · Date: 2026-10-18

## Context
`gengar.me` already exists as a CyberPanel site on the server reached by `ssh cyberpanel`, currently serving the placeholder page. Larder deploys by uploading a built tarball, unpacking it into `app/releases/<release>`, linking shared `.env` and `storage`, and switching `app/current` with `mv -Tf`. Movies runs `git pull` and `npm` on the server instead.

## Decision
- Copy Larder's pattern. `deploy/deploy.sh` runs locally (tests, `composer install --no-dev`, `npm run build`, tarball, `scp`), then calls `deploy/remote-deploy.sh` on the server.
- `public_html` is a symlink to `app/current/public`. Keep three releases.
- Run all `artisan` commands with `/usr/local/lsws/lsphp84/bin/php` as the site user `genga7347`.
- The server has no deploy key for this repo and no Node build.

## Alternatives rejected
- `git pull` on the server, as Movies does. It needs a deploy key and Node on the server, and a failed build mid-pull leaves the live site half updated.
- GitHub Actions deploying over SSH. A reasonable later step, but it puts a server key into GitHub secrets for a site deployed a few times a year.
- Cloudflare Pages. It needs a static export (rejected in ADR-001).

## Consequences
- A failed build never reaches the server, and a failed remote step leaves `current` unchanged.
- Rollback is pointing `current` at the previous release.
- Deploys need Jack's machine and SSH access.
