# gengar.me Portfolio - Tasks

Last Updated: 2026-10-18

## How to use this file
- Before a slice: read its "Before starting" block, load the listed skills, read the listed ADRs and spec sections.
- Each task fits one agent session and names the requirement IDs it satisfies.
- Tests are part of the task: write the failing test first, then the code. There is no separate testing phase.
- Review tags: `routine` (established pattern), `review closely` (security, data integrity, money), `novel` (new pattern or judgement call; review the approach first).
- **Owner writes** marks business logic the user writes; the agent prepares the code and tests around a `TODO(owner)`.
- When a task is done: tick it, add the commit hash, and record any deviation in `portfolio-site-context.md`.
- Commit messages start with the task ID. No AI attribution in commits or PRs.
- Feature tests (anything through HTTP) go in `tests/Feature`, unit tests (value objects, config parsing) in `tests/Unit`. Pest `it()`/`test()` descriptions are plain English. Use `getJson` only for JSON routes; HTML pages use `get` with response helpers (`assertOk`, `assertNotFound`).
- Run PHP with `herd php` only if `which php` does not resolve to Herd; otherwise plain `php`.

## Database safety
The app has no database (ADR-001). These rules still apply:
- Never edit `.env`, `.env.testing`, `phpunit.xml` or database configuration without explicit approval.
- Do not add `RefreshDatabase` or any database trait to tests. If a task seems to need a database, stop and ask.
- Never run `migrate:fresh`, `migrate:reset`, `migrate:refresh`, `migrate:rollback`, `db:wipe` or a seeder on the server.

---

## S0: Scaffold (shared prerequisite)

**Before starting**
- Agents: `laravel-backend-developer`, `scout` (for running CI checks)
- Skills: `laravel-backend-defaults`, `model-router`
- Read: ADR-001, ADR-003, spec section 7.5

- [x] **S0-01** (`953e43a`) Create a Laravel 13 app in `~/Work/gengar.me` (no starter kit), drop the database, queue and session config it does not need, and push to a new public repo `MajorasJack/gengar.me` · Req: NFR-6 · Files: `composer.json`, `config/`, `.gitignore` · `routine` · Done when `php artisan about` shows no database driver in use and the repo is on GitHub.
- [x] **S0-02** (`cfb3528`, `cbd50fa`) Add Pest 4, Pint and Larastan level 8; add a GitHub Actions workflow running all three plus a step that fails if `.env`, `*.pem` or `id_*` files are tracked · Req: NFR-5, NFR-6 · Files: `.github/workflows/ci.yml`, `phpstan.neon`, `pint.json` · `routine` · Done when CI is green on `main` and a test branch with a committed `.env` fails.
- [x] **S0-03** (#1) Define a `public` route group without session, cookie and CSRF middleware; route `/` to `PortfolioController@home` returning the layout · Req: NFR-4 · Files: `bootstrap/app.php`, `routes/web.php`, `app/Http/Controllers/PortfolioController.php`, `tests/Feature/HomePageTest.php` · `review closely` · Done when a test asserts `/` is OK and the response has no `Set-Cookie` header.
- [x] **S0-04** (#2) Build the layout and design tokens from mockup C (palette, type scale, spacing) in `resources/css/app.css`; self-host Geist and Geist Mono as WOFF2 subsets with `font-display: swap` · Req: UI-1, NFR-4 · Files: `resources/views/layouts/site.blade.php`, `resources/css/app.css`, `public/fonts/` · `novel` · Done when the rendered layout matches mockup C's header and the HTML contains no third-party URLs (test).
- [x] **S0-05** (#3) Configure Vite to emit CSS only for public pages, with no JavaScript entry · Req: UI-4 · Files: `vite.config.js`, `resources/views/layouts/site.blade.php` · `routine` · Done when a feature test asserts the homepage HTML has no `<script` tag.
- [x] **S0-06** (#4) Create `config/portfolio.php` with the shape from ADR-003 and a `Project` readonly value object with `fromArray()`; validate required keys and throw a clear exception on a missing one · Req: CONT-3 · Files: `config/portfolio.php`, `app/Portfolio/Project.php`, `tests/Unit/Portfolio/ProjectTest.php` · `routine` · Done when unit tests cover a valid entry, a private entry with no URL, and a missing-key exception.

**S0 review checkpoint**
- [x] `feature-dev:code-reviewer` review; Critical and High findings fixed (#5: `/up` third-party assets fixed; route list pinned; checks run on every public response)
- [x] Demo: CI green; `/` renders the layout with no cookies set (checked 2026-10-18, 27 tests on `028b7e6`)
- [x] Sign-off: Reviewer Jack · Date 2026-10-19 · Status: Approved

---

## S1: Homepage

**Before starting**
- Agents: `laravel-backend-developer`, `builder` (CSS layout)
- Skills: `laravel-backend-defaults`, `frontend-design`, `web-design-guidelines`
- Read: spec sections 3, 7.1, 7.2, 7.3; `research/01-project-inventory.md`; mockup C

- [x] **S1-01** (#7) Render the header (name, role, location, remote note, intro paragraph, contact links, CV button) from `config/portfolio.php` · Req: CONT-1, CONTACT-1 · Files: `resources/views/partials/header.blade.php`, `tests/Feature/HomePageTest.php` · `routine` · Done when tests assert each field and that `jack@gengar.me` appears as literal text and inside a `mailto:` link.
- [x] **S1-02** (#8) **Owner writes** the intro paragraph and the four "what I do" descriptions in config, following the public bio rules (no employer names, no speed figures); the agent renders the section and a test enforcing the 70-word limit · Req: CONT-1, CONT-2 · Files: `config/portfolio.php` (`TODO(owner)`), `resources/views/partials/what-i-do.blade.php` · `routine` · Done when the owner has replaced the `TODO(owner)` copy and the word-limit test passes.
- [x] **S1-03** (#9) Fill the five featured projects in config from the inventory; check each public URL returns a HTTP `200` and record the result in the context file · Req: CONT-3, CONT-4 · Files: `config/portfolio.php`, `portfolio-site-context.md` · `routine` · Done when all five entries exist with stack, test count, `tests_checked_at` and either `url` or `visibility`.
- [x] **S1-04** (#10) Render the projects table and the smaller tools list; below 640px render rows as stacked blocks with labels · Req: CONT-3, CONT-4, CONT-5, UI-3 · Files: `resources/views/partials/projects.blade.php`, `resources/css/app.css`, `tests/Feature/HomePageTest.php` · `routine` · Done when tests assert config order, that a private project has its label and no `<a>`, and the page has no horizontal overflow at 360px (checked by hand and noted).
- [x] **S1-05** (#12) Fill the denylist in `config/portfolio.php` with every employer in the CV profile plus the shop and label names, stored as hashes (names are not listed in the repo), for Jack to check; write a test that renders every public route and asserts no denylisted name and no `\d+(\.\d+)?s\s*→\s*\d` figure appears · Req: CONT-6 · Files: `config/portfolio.php`, `tests/Feature/PublicCopyTest.php` · `review closely` · Done when Jack has confirmed the list, the test passes, and it fails when a denylisted name is added to the intro.
- [x] **S1-06** (#13) Compile the portfolio CV (see open question 1), copy it to `public/cv.pdf`, and test the route serves it as `application/pdf` with no phone number in its extracted text · Req: CONTACT-2, CONTACT-3 · Files: `public/cv.pdf`, `tests/Feature/CvDownloadTest.php`, `~/Work/ai-job-search/cv/main_portfolio.tex` · `review closely` · Done when the test passes and the PDF has been read visually.
- [x] **S1-07** (#14) Show "available for work" only while `portfolio.available` is true; assert that no page contains a phone number pattern · Req: CONTACT-2, CONTACT-4 · Files: `resources/views/partials/header.blade.php`, `tests/Feature/HomePageTest.php` · `routine` · Done when tests cover both flag states and the phone check.
- [x] **S1-08** (#15) Check contrast for every text/background token pair, focus states, and the homepage transfer size · Req: NFR-1, NFR-3 · Files: `resources/css/app.css` · `routine` · Done when every pair meets AA, each link shows a visible focus ring, and the transfer size is under 150 KB (numbers recorded in context).

**S1 review checkpoint**
- [x] `code-auditor` review; Critical and High findings fixed (#16: flaky denylist test fixed by a six-word limit; history exposure already accepted, context row corrected; Low findings fixed)
- [x] Demo: open `/` at 1280px and 360px and read every section; download the CV (checked 2026-10-18 on `6660ed2` in headless Chromium: no horizontal overflow at 360px, `/cv.pdf` served as `application/pdf`, no cookies on `/`)
- [x] Sign-off: Reviewer Jack · Date 2026-10-19 · Status: Approved

---

## S2: Live on gengar.me

**Before starting**
- Agents: `builder` (server work), `scout` (reading logs)
- Skills: `laravel-backend-defaults`
- Read: ADR-002, spec section 7.4, `~/Work/recipes/deploy/remote-deploy.sh`

Server changes are outward-facing. Confirm with Jack before replacing `public_html` or changing DNS.

- [x] **S2-01** (#23) Write `deploy/deploy.sh` (local) to run tests, build assets, `composer install --no-dev`, pack a tarball and upload it with `scp` to `cyberpanel` · Req: OPS-1 · Files: `deploy/deploy.sh` · `routine` · Done when a dry run produces a tarball under 5 MB with no `.env`, `tests/` or `node_modules/` inside.
- [x] **S2-02** (#24) Write `deploy/remote-deploy.sh` from Larder's script: unpack to `app/releases/<timestamp>`, link shared `.env` and `storage`, cache config, routes and views with `lsphp84`, switch `current` with `mv -Tf`, keep three releases · Req: OPS-1, OPS-2, OPS-3 · Files: `deploy/remote-deploy.sh` · `review closely` · Done when a failing `artisan` step in a test run leaves `current` unchanged.
- [x] **S2-03** (live 2026-10-19, release `20261019162944-df6d084`) With Jack's approval, create `/home/gengar.me/app/shared/.env` (no secrets beyond `APP_KEY`), move the placeholder `public_html` aside, and point `public_html` at `app/current/public` · Req: OPS-1 · Files: server only · `review closely` · Done when `https://gengar.me` serves the homepage over HTTPS.
- [x] **S2-04** (awaiting Jack: uptime monitor) Confirm `/up` returns a HTTP `200` and add it to whatever uptime check covers the sibling sites · Req: OPS-4 · Files: `bootstrap/app.php`, `tests/Feature/HealthCheckTest.php` · `routine` · Done when the test passes and `curl -I https://gengar.me/up` returns `200`.
- [x] **S2-05** (#28, live 2026-10-19) Redirect `www.gengar.me` and HTTP to `https://gengar.me` with a HTTP `301`, keeping the path · Req: OPS-5 · Files: `app/Http/Middleware/RedirectWwwToCanonicalHost.php`, `bootstrap/app.php` (HTTP to HTTPS is Cloudflare's) · `routine` · Done when `curl -I http://www.gengar.me/projects` returns `301` to `https://gengar.me/projects`.
- [x] **S2-06** (rollback tested live 2026-10-19) Document deploy and rollback in `deploy/README.md` · Req: OPS-3 · Files: `deploy/README.md` · `routine` · Done when a rollback following the README restores the previous release.

**S2 review checkpoint**
- [x] `feature-dev:code-reviewer` review of both scripts; Critical and High findings fixed (no Critical or High; the five Medium and the Low findings fixed in #25 before the first real run)
- [x] Demo: visit `https://gengar.me`, run a deploy, roll back by one symlink (2026-10-19: three deploys run, rollback to the previous release and forward again following `deploy/README.md`)
- [ ] Jack reads the live homepage and adjusts copy before S3
- [ ] Sign-off: Reviewer ____ · Date ____ · Status: Pending / Approved / Issues found

---

## S3: Project pages

**Before starting**
- Agents: `laravel-backend-developer`
- Skills: `laravel-backend-defaults`, `frontend-design`
- Read: ADR-003, spec section 7.1, each project's README or `PRODUCT.md`

- [x] **S3-01** (#34) Add `/projects/{slug}` to `PortfolioController@project`, named `projects.show` (the sitemap keys on it, S4-03), resolving the slug from config; unknown slugs abort with `Response::HTTP_NOT_FOUND` · Req: CONT-7, CONT-8 · Files: `routes/web.php`, `app/Http/Controllers/PortfolioController.php`, `tests/Feature/ProjectPageTest.php` · `routine` · Done when tests cover a known slug, an unknown slug and a private project.
- [x] **S3-02** (#35) Build the detail view (problem, what was built, engineering notes, stack, link or label, tests checked date) and a styled `404` view · Req: CONT-7, CONT-8 · Files: `resources/views/project.blade.php`, `resources/views/errors/404.blade.php` · `routine` · Done when both views match the site layout and tests assert each section heading.
- [x] **S3-03** (#36) Draft detail copy for each of the five projects from their repos (`PRODUCT.md`, READMEs, module lists) for Jack to edit, following the public bio rules · Req: CONT-7, CONT-6 · Files: `config/portfolio.php` · `routine` · Done when Jack has approved each entry and `PublicCopyTest` still passes.
- [ ] **S3-04** Link each table row name to its detail page · Req: CONT-7 · Files: `resources/views/partials/projects.blade.php` · `routine` · Done when a test asserts each row links to `/projects/{slug}`.
- [ ] **S3-05** (Could) Add Promotr and DrinkSafe screenshots from their `brag-output/brag.jpg`, converted to WebP with `width` and `height` set and `loading="lazy"` · Req: CONT-7, NFR-1 · Files: `public/images/projects/` · `routine` · Done when images render without layout shift and the page stays inside the size budget.

**S3 review checkpoint**
- [ ] `code-auditor` review; Critical and High findings fixed
- [ ] Demo: click Promotr in the table and read `/projects/promotr`; `/projects/nope` shows the styled 404
- [ ] Sign-off: Reviewer ____ · Date ____ · Status: Pending / Approved / Issues found

---

## S4: Shareable and findable

**Before starting**
- Agents: `builder`
- Skills: `frontend-design`, `web-design-guidelines`
- Read: spec sections 7.3 and 7.5

- [ ] **S4-01** Add the light palette under `prefers-color-scheme: light`, keeping dark as the default when there is no preference · Req: UI-2, NFR-3 · Files: `resources/css/app.css` · `routine` · Done when both palettes pass AA contrast and the accent reads on both.
- [x] **S4-02** (#32) Add meta description, canonical URL and Open Graph/Twitter tags per page, plus a 1200x630 static Open Graph image in the direction C style · Req: NFR-7 · Files: `resources/views/layouts/site.blade.php`, `public/og.png`, `tests/Feature/MetaTagsTest.php` · `routine` · Done when tests assert the tags on `/` and a detail page.
- [x] **S4-03** (#33) Serve `sitemap.xml` (home plus detail pages, from config) and `robots.txt` · Req: NFR-7 · Files: `routes/web.php`, `resources/views/sitemap.blade.php`, `public/robots.txt`, `tests/Feature/SitemapTest.php` · `routine` · Done when the test asserts every featured project URL appears in the sitemap.
- [ ] **S4-04** Run Lighthouse (mobile) on the live homepage and one detail page; fix anything below target · Req: NFR-2 · Files: as needed · `routine` · Done when scores are at least 95 performance and 100 accessibility, recorded in context.
- [x] **S4-05** (#31) Add a favicon in the direction C style: an SVG icon (a lowercase `g` in the accent colour on the dark background) as `public/favicon.svg`, plus `public/favicon.ico` (32x32) and `public/apple-touch-icon.png` (180x180), linked from the layout · Req: NFR-4, NFR-7 · Files: `public/favicon.*`, `public/apple-touch-icon.png`, `resources/views/layouts/site.blade.php`, `tests/Feature/FaviconTest.php` · `routine` · Done when the layout links all three, each file returns 200 locally, and the `public responses` tests still pass.

**S4 review checkpoint**
- [x] `feature-dev:code-reviewer` review; Critical and High findings fixed (#5: `/up` third-party assets fixed; route list pinned; checks run on every public response)
- [ ] Demo: paste the URL into Slack and see the preview card; Lighthouse report meets targets
- [ ] Sign-off: Reviewer ____ · Date ____ · Status: Pending / Approved / Issues found
