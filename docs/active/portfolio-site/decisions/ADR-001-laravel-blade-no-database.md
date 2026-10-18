# ADR-001: Laravel 13 with Blade, no database

Status: Accepted · Date: 2026-10-18

## Context
The site holds a few hundred words of content that changes a few times a year. Jack is a backend PHP engineer, and the public repo doubles as a code sample (spec G3). Every sibling site on the server is Laravel on `lsphp84`, so the hosting setup is already known.

## Decision
- Laravel 13 on PHP 8.4, standard (non-modular) layout. There is one feature area, so modules would add folders without separating anything.
- Blade views with hand-written CSS built by Vite. No Inertia, Vue or Livewire, and no JavaScript on public pages.
- No database, queue or session store. Content comes from config (ADR-003).
- Pest 4, Pint and Larastan level 8 in CI.

## Alternatives rejected
- Plain static HTML. Cheapest, but it shows nothing of Jack's PHP work and makes content edits repetitive across pages.
- Laravel exported to static files. Adds an export step for little gain, since PHP hosting already exists.
- Inertia and Vue, as in the other projects. A JavaScript runtime for a read-only page goes against the "quiet over clever" principle and adds front-end code Jack would maintain through AI only.

## Consequences
- Pages are server-rendered and cached with `config:cache`, `route:cache` and `view:cache`, so response times stay low without a page cache.
- With no database there is no data to back up and nothing to migrate.
- If a blog or contact form is ever wanted, that is a new decision.
