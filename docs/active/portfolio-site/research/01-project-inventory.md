# Project inventory

Last Updated: 2026-10-18

Gathered from the local repos under `~/Work` (README, `composer.json`, `package.json`, `.env.example`, git log) and a read-only look at the CyberPanel server. No URLs were opened. Test counts are regex counts of `it(`, `test(` and `function test` in tracked files, so they are approximate (UNCERTAIN until each suite is listed in S1-03).

## Featured
| Project | Repo folder | What it does | Stack | Tests | Where | Last commit |
|---|---|---|---|---|---|---|
| Promotr | `~/Work/promotr` (`MajorasJack/backline`) | Event management for live-music promoters: budgets, tours, artists, contracts, ticketing | Laravel 12, PHP 8.4, Inertia 2, Vue 3.5, Tailwind 4, Reverb, Scout + Meilisearch, Cashier, Fortify, Sanctum; about 50 modules | ~7,600 Pest | `promotr.co` (from docs, UNCERTAIN) | 2026-09-29 |
| Larder | `~/Work/recipes` (`MajorasJack/larder`) | Household recipes and pantry PWA. Imports recipes from links, video and photos with Claude, matches them to stock, builds the shopping list | Laravel 13, PostgreSQL 17, Vue 3.5 SPA, Sanctum, passkeys, Dexie/workbox, Anthropic SDK | ~1,400 Pest | Invite only; name is a working title | 2026-10-18 |
| Darkroom | `~/Work/images` (`MajorasJack/image-mastery`) | Image tools: background and object removal, upscaling, conversion, image to PDF, compression | Laravel 13, Inertia 3, Vue 3.5, Fortify + passkeys; Python FastAPI service with pyvips, rembg/ONNX, OpenCV | ~107 Pest, 40 PHPUnit, 41 pytest | `darkroom.gengar.me` | 2026-10-18 |
| DrinkSafe | `~/Work/drinksafe.app` | Community drink-spiking reports, venue safety scores, incident heatmap | Laravel 13, Inertia 3, Vue 3.5, Leaflet, Turnstile, Fortify, Pest 4 browser tests | ~190 | `drinksafe.app` (UNCERTAIN) | 2026-10-08 |
| Movies | `~/Work/movies` (`MajorasJack/movies`) | Catalogue for a physical film collection, filled in from a barcode scan | Laravel 13, Inertia 3, React 19, TMDB, OMDb; modular | ~2,300 Pest, ~255 Vitest | `movies.gengar.me` | 2026-10-14 |

## Smaller tools (no shop or supplier names on the site)
- Invoice Inbox (`~/Work/accounting`): pulls supplier invoices from Gmail, reads them with Claude, posts them to Xero. Laravel 13, ~3,000 tests.
- Pre-orders and back-in-stock Shopify app (`~/Work/preorder-app`): React Router app with a storefront extension, hosted on Fly.io.
- Shopify image optimiser (`~/Work/shopify-image-optimiser`): PHP CLI on AWS Lambda that compresses images and suggests alt text with Claude for a person to approve. ~115 PHPUnit tests.

## Excluded
Tech tests and employer work, WordPress client sites (two client and shop sites), marketing folders, `conventionator` (another person's repo), `Music-Metadata-App` (generated with v0) and `FormatShopifyAddresses` (stale).

## Server
`ssh cyberpanel` hosts `gengar.me` (placeholder), `darkroom`, `larder`, `larder-staging`, `movies` and `accounting` subdomains, and `drinksafe.app`. PHP 8.2 to 8.5 are installed as `lsphp`; sibling sites use `lsphp84`.

## Summary
Five deployed or near-deployed Laravel projects, with test suites from about 190 to about 7,600 tests, plus three smaller tools. All but Movies use Vue on the front end. Three facts need checking during the build: whether `promotr.co` and `drinksafe.app` are live, the exact test counts, and which repos are public.
