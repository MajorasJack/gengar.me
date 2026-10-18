# gengar.me Portfolio - Context

Last Updated: 2026-10-18

Reference material, read when needed rather than before every task.

## Document map
| File | Holds | Read when |
|---|---|---|
| `portfolio-site-spec.md` | Goals, scope, requirements, open questions | Before any slice |
| `portfolio-site-plan.md` | Approach, slices, risks | Before any slice |
| `portfolio-site-tasks.md` | Checklist | Every task |
| `decisions/ADR-001` to `ADR-003` | Stack, deploy, content storage | Named in a slice's "Before starting" |
| `research/01-project-inventory.md` | Facts about each project | Writing project copy |

## Project conventions that apply
- Standard Laravel layout, not modular (ADR-001). Tests in `tests/Feature` and `tests/Unit`, Pest, AAA, one behaviour per test, no comments in tests.
- Return types and parameter types everywhere, PHP 8.4 syntax, `sprintf` over concatenation, imported class names.
- Named arguments are all-or-nothing on our own multi-parameter methods.
- British spelling in code and copy.
- Public bio rules for all site copy: no past employer names, no raw performance figures. Mention Claude Code by name when describing AI-assisted work.
- No AI attribution in commits, PRs or code.

## Key files and references
- Design mockups (direction C chosen): https://claude.ai/code/artifact/43528df5-d245-4b47-a4fe-ae604c519780
- Server: `ssh cyberpanel`; site home `/home/gengar.me`, site user `genga7347`, PHP `/usr/local/lsws/lsphp84/bin/php`.
- Deploy template: `~/Work/recipes/deploy/remote-deploy.sh` (Larder).
- Candidate profile: `~/Work/ai-job-search/CLAUDE.md`; CV sources: `~/Work/ai-job-search/cv/`.
- Project repos: `~/Work/promotr`, `~/Work/recipes` (Larder), `~/Work/images` (Darkroom), `~/Work/drinksafe.app`, `~/Work/movies`.

## Unverified facts to check during the build
| Fact | Checked in task |
|---|---|
| `promotr.co` and `drinksafe.app` are publicly live | S1-03 |
| Test counts in the inventory (regex counts, approximate) | S1-03 (run each suite's `--list-tests` or count with Pest) |
| Which GitHub repos are public, so detail pages can link source | S3-03 |
| DNS for `gengar.me` is on Cloudflare | S2-05 |
| How Larder's script handles PHP workers holding old release paths | S2-02 |

## Decisions made during build
| Date | Task | Decision and reason |
|---|---|---|
| 2026-10-18 | Spec | Direction C, public repo, five featured projects plus smaller tools, Larder shown as private beta, contact by email/LinkedIn/GitHub only, CV download and availability flag (all chosen by Jack) |
| 2026-10-18 | Spec | Public CV is a portfolio variant without the phone number; denylist is all CV employers plus Unusual Horror and its shops; project links are checked in S1-03 and unlinked if not live (chosen by Jack) |
| 2026-10-18 | Spec | Larder keeps its name, no photo, dark palette when the system has no preference, pre-orders app unlinked (chosen by Jack) |
| 2026-10-18 | S0-01 | Scaffolded Laravel 13.37 with `composer create-project`. Removed `database/`, `app/Models`, and the `database`, `queue`, `session` and `auth` config files; `.env.example` uses `DB_CONNECTION=null`, `SESSION_DRIVER=array`, `QUEUE_CONNECTION=sync`, `CACHE_STORE=file`. The unused sqlite `DB_*` env vars in `phpunit.xml` were removed with Jack's approval. The generated `AGENTS.md` and `CLAUDE.md` (generic Laravel Boost instructions) were deleted at Jack's request |
| 2026-10-18 | S0-02 | Pest 4.x, Pint (Laravel preset plus `declare_strict_types`) and Larastan 3 at level 8. Larastan analyses `app`, `bootstrap/app.php`, `config` and `routes` but not `tests`, because it reads `$this` in Pest closures as `TestCall`. Removed the `public` filesystem disk and its storage link: no uploads, and its `rtrim(env(...))` failed level 8. CI commands: `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --no-progress --memory-limit=512M`, `vendor/bin/pest`. Verified: CI green on branch `s0-02-ci`; a throwaway branch with a committed `.env` failed at the secrets step (branch deleted afterwards). A local hook blocks pushing to the default branch, and the HTTPS remote has no credentials, so pushes went over SSH to `s0-02-ci`; the default branch still needs fast-forwarding to it |
| 2026-10-18 | S0-03 | `routes/web.php` is loaded through `withRouting(then: ...)` under a `public` middleware group holding only `SubstituteBindings`, not the `web` group, so no session, cookie or CSRF middleware runs. `/up` stays on the framework health route. Removed the default `welcome` view and `ExampleTest`; `home.blade.php` extends a bare `layouts/site.blade.php` for S0-04. Set the `local` disk's `serve` to false so Laravel registers no `storage/{path}` GET or PUT routes (the site stores no files); a test asserts both routes are absent |
| 2026-10-18 | S0-04 | CSS is hand-written; Tailwind and the Bunny fonts Vite plugin were removed. Fonts are Latin subsets (Basic Latin, Latin-1, common punctuation and arrows) of the Geist 1.5.1 variable faces from npm, clamped with fonttools to weights 400-600 (sans, 15 KB) and 400-500 (mono, 16 KB), in `public/fonts/` with the OFL. CI builds assets (`setup-node` LTS, `npm ci`, `npm run build`) before Pest, so tests render the real `@vite` tags; `package-lock.json` is committed for `npm ci`. `AppServiceProvider` makes Vite emit root-relative asset URLs (`/build/...`), so the third-party test can fail on any absolute `href`/`src` on `link`, `script`, `img`, `iframe` or `source` (checked: it fails with a Bunny fonts `<link>` added). Header markup is styled but not rendered yet (S1-01); checked by screenshot with mockup C's header at 1280px. At 360px the projects table overflows its column, which S1-04 handles |
| 2026-10-18 | S0-05 | Deleted `resources/js/app.js` and removed it from the Vite inputs; the manifest now holds only `resources/css/app.css`. A feature test asserts the homepage has no `<script` tag (checked: it fails with a `<script>` added to the layout). `npm run dev` still injects the Vite client script, which is expected and only affects local hot reload |
| 2026-10-18 | S0-06 | `config/portfolio.php` holds the ADR-003 keys with empty values; S1 tasks fill them. `Project` is a `final readonly` class with required keys `slug`, `name`, `summary`, `stack`, `tests`, `tests_checked_at`, `status`; `url` and `visibility` default to null. Beyond ADR-003, `fromArray()` also throws when both `url` and `visibility` are null, since CONT-4 needs a label in place of the host. `detail` is not read yet (S3). Exceptions are `App\Portfolio\Exceptions\InvalidProjectException` |
| 2026-10-18 | S0-review | `/up` is now `HealthCheckController` in `routes/web.php` (public group): it dispatches `DiagnosingHealth` and returns plain-text `ok`, because the framework's health page loaded Bunny fonts and a jsDelivr script; `/up` stays exempt from maintenance mode. A test pins the registered URIs to exactly `/` and `up`, replacing the storage-route test (checked: it fails with the local disk's `serve` set to true). The no-cookie, no-third-party and no-JavaScript tests run against `/`, `/up` and a 404 through a dataset; the third-party pattern now also covers `video`, `audio`, `embed`, `object` and `form action` with whitespace before the URL, and the JavaScript pattern also fails on `javascript:` URLs, inline `on...=` handlers and `rel="modulepreload"` (each checked by adding it to the layout). `Project::$status` is the `ProjectStatus` enum, and an unknown value throws naming the slug and value. `.env.example` has `APP_DEBUG=false`. CI has read-only `contents` permission, cancels superseded runs per ref, times out after 10 minutes, and its secrets check matches SSH key names and `*.key` instead of any `id_*` file |
