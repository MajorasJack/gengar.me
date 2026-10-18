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
