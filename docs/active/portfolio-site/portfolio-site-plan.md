# gengar.me Portfolio - Plan

Last Updated: 2026-10-18

This file covers how and in what order. Requirements are in `portfolio-site-spec.md`; tasks in `portfolio-site-tasks.md`. Acceptance criteria are not repeated here.

## Summary
A small Laravel 13 app with Blade views and no database replaces the CyberPanel placeholder at `gengar.me`. All content lives in `config/portfolio.php`, so adding a project or switching off "available for work" is a one-line change. CSS is hand-written from the direction C tokens and built with Vite; public pages ship no JavaScript.

The homepage arrives first and goes live on the server before anything else is added. Project detail pages and the sharing and SEO work follow, and can run in parallel.

## Current state
- Greenfield. No `gengar.me` repo exists under `~/Work`.
- The server (`ssh cyberpanel`) serves `/home/gengar.me/public_html/index.html`, the CyberPanel placeholder. Site user is `genga7347`.
- Sibling sites (Larder, Darkroom, Movies, Accounting) already serve `app/current/public` through a `public_html` symlink, using `lsphp84`. Larder's `deploy/remote-deploy.sh` (tarball, shared `.env` and `storage`, `mv -Tf` symlink switch, pruning old releases) is the template.
- Project facts come from `research/01-project-inventory.md`.

## Target state
- Public repo `MajorasJack/gengar.me` with CI (Pest, Pint, Larastan, secret check).
- `https://gengar.me` serves the homepage, five detail pages, `/cv.pdf`, `/up`, `sitemap.xml` and `robots.txt`.
- `deploy/deploy.sh` (local) and `deploy/remote-deploy.sh` (server) perform tarball release deploys with three releases kept, adapted from Larder's scripts.

## Approach
- Standard Laravel layout (ADR-001). One controller, `PortfolioController`, with `home` and `project` actions; a `Project` readonly value object built from config (ADR-003).
- Views in `resources/views/` with one layout and partials per section. Tokens from mockup C go into `resources/css/app.css`.
- Geist and Geist Mono are self-hosted as WOFF2 subsets (NFR-4).
- Session, cookie and CSRF middleware are removed from the public route group so no cookies are set (NFR-4).
- `/cv.pdf` is a static file in `public/`, copied from the job-search repo's compiled portfolio CV.
- Deploy follows ADR-002.

## Slices
| Slice | Outcome | Requirements | Demo |
|---|---|---|---|
| S0 Scaffold (shared prerequisite) | Empty app with CI, tokens and fonts | NFR-4, NFR-5, NFR-6, UI-1, UI-4 | CI green; `/` renders the layout with no cookies set |
| S1 Homepage | Full homepage with real content | CONT-1 to CONT-6, CONTACT-1 to CONTACT-4, UI-3, NFR-1, NFR-3 | Open `/` at 1280px and 360px and read every section; download the CV |
| S2 Live on gengar.me | Site served from the server with release deploys | OPS-1 to OPS-5 | Visit `https://gengar.me`, run a deploy, roll back by one symlink |
| S3 Project pages | A detail page per featured project | CONT-7, CONT-8 | Click Promotr in the table and read `/projects/promotr`; `/projects/nope` shows the styled 404 |
| S4 Shareable and findable | Light theme, link previews, sitemap, Lighthouse targets met | UI-2, NFR-2, NFR-7 | Paste the URL into Slack and see the preview card; Lighthouse report meets targets |

S3 and S4 can run in parallel after S2. Jack should read the live homepage after S2 and adjust the copy before S3 starts.

## Risks
| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Copy names a past employer or a shop | Medium | Medium | CONT-6 test against the denylist |
| Test counts go stale | High | Low | "Last checked" date in config, shown on detail pages |
| PHP workers keep the old release's paths cached after the switch | Medium | Medium | Follow Larder's handling in `remote-deploy.sh` (check in S2-02) |
| CV PDF leaks the phone number | Medium | Medium | Open question 1; test that `/cv.pdf` text has no phone pattern |
| Linking a project that is down | Low | Low | Open question 2; check each URL during S1-03 |

## Success metrics
| Metric | Target |
|---|---|
| Lighthouse mobile performance / accessibility | at least 95 / 100 |
| Homepage transfer size | under 150 KB |
| Time to add a project | one config entry and a deploy |

## Dependencies
- SSH access to `cyberpanel` as root for the vhost and `deploy.sh`.
- DNS for `gengar.me` and `www` (Cloudflare, per the sibling deploy docs).
- A compiled portfolio CV from `~/Work/ai-job-search/cv/`.
- GitHub repo `MajorasJack/gengar.me` and a deploy key for the server.
