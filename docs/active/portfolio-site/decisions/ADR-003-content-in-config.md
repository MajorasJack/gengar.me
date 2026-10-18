# ADR-003: Site content lives in `config/portfolio.php`

Status: Accepted · Date: 2026-10-18

## Context
The content is a profile, four "what I do" entries, five featured projects, three smaller tools and a denylist. Jack edits it rarely, and every edit ships with a deploy anyway.

## Decision
- One PHP config file holds all content. Projects are an ordered list of arrays, turned into a readonly `App\Portfolio\Project` value object by `Project::fromArray()`.
- Project keys: `slug`, `name`, `summary`, `stack` (list), `tests` (int, approximate), `tests_checked_at` (`Y-m-d`), `url` (nullable), `visibility` (label used when `url` is null), `status` (`live` or `private`), `detail` (problem, built, notes, for S3).
- Top-level keys: `available` (bool), `profile`, `what_i_do`, `smaller_tools`, `denylist`.
- `fromArray()` throws on a missing required key, so a broken entry fails in tests and at `config:cache`, never on the live page.

## Alternatives rejected
- Markdown files with front matter. Better for long prose, but it needs a parser dependency and the detail copy is short.
- SQLite with an admin page. Adds a database, backups and auth for content that one person changes a few times a year.

## Consequences
- Adding a project is one array entry and a deploy.
- Copy changes are reviewable in git diffs, and `PublicCopyTest` checks them against the denylist.
- Long-form writing (a blog) would need a different store.
