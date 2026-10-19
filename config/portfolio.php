<?php

declare(strict_types=1);

/*
 * All site content (ADR-003). Each entry in `projects` becomes an
 * App\Portfolio\Project through Project::fromArray(), in this order.
 */

return [
    'available' => true,

    // Requests on `www.` this host get a 301 to it (OPS-5).
    'canonical_host' => 'gengar.me',

    'profile' => [
        'name' => 'Jack Evans',
        'role' => 'senior PHP / Laravel engineer',
        'location' => 'Manchester, UK',
        'remote' => 'remote-first',
        // 70 words at most (CONT-1).
        // Meta and Open Graph description, under 160 characters.
        'description' => 'Jack Evans is a senior PHP and Laravel engineer in Manchester, UK, with fourteen years building backend systems. Running projects, CV and contact details.',
        'intro' => "I've spent fourteen years building backend systems in PHP and Laravel, most recently on a healthcare platform used across several countries, and before that in food delivery, legal tech and e-commerce. Alongside the code, I tend to end up writing the testing guides, RFCs and code review process a team works to. I work remotely and thrive when given end-to-end ownership of a specific project or focus area.",
        'email' => 'jack@gengar.me',
        'linkedin' => 'https://www.linkedin.com/in/jack-evans-2526a1a3',
        'github' => 'https://github.com/MajorasJack',
        'cv' => '/cv.pdf',
    ],

    // One sentence each, with no employer names and no performance figures (CONT-2).
    'what_i_do' => [
        ['title' => 'Performance', 'description' => 'Profiling slow pages, rewriting queries and adding the indexes that were missing.'],
        ['title' => 'APIs and integrations', 'description' => 'Designing APIs, and connecting to delivery platforms, payment providers and national eID services.'],
        ['title' => 'Engineering standards', 'description' => 'Writing the testing guides, RFCs and code review process that a team works to.'],
        ['title' => 'AI-assisted development', 'description' => 'Using Claude Code across the stack, building front ends AI-first while I design the architecture and backend and review every change.'],
    ],

    // Keys per entry: slug, name, summary, stack (list), tests (int, approximate),
    // tests_checked_at (Y-m-d), url (nullable), visibility (label when url is null),
    // status (live or private), detail (problem, built, notes; S3), source (public repo url,
    // optional). tests is the number of
    // test functions in the PHP suite, from `pest --list-tests` with dataset rows collapsed.
    // TODO(owner): every `detail` below is a draft from the project's own docs, waiting on Jack to approve or edit.
    'projects' => [
        [
            'slug' => 'promotr',
            'name' => 'Promotr',
            'summary' => 'Event management for live-music promoters: budgets, tours, artists, contracts and ticketing.',
            'stack' => ['Laravel 12', 'Inertia 2', 'Vue 3', 'Reverb', 'Meilisearch', 'Cashier'],
            'tests' => 29753,
            'tests_checked_at' => '2026-10-18',
            'url' => 'https://promotr.co',
            'visibility' => null,
            'status' => 'live',
            'detail' => [
                'problem' => 'Promoters run budgets, tours, artists and contracts across spreadsheets and separate tools. Budgets are the hard part, because costs and ticket sales keep moving until the night of the show.',
                'built' => 'A multi-tenant platform for promoters, booking agents, venues and artists. It covers event and tour budgets, artist guarantees, contracts from templates, tasks, ticket sales and public pages for events, brands and artists. I built the Vue front end AI-first with Claude Code, and I own the architecture, data model, tests and code review.',
                'notes' => [
                    'Over 50 Laravel modules. Each keeps its own tests, and most also hold their own routes, migrations, policies and Vue pages.',
                    'Nearly every record belongs to one promoter, venue, booking agency or artist. A company holds only billing details and has no records of its own.',
                    'One ticketing platform\'s API does not return sales, so its daily report emails are parsed instead. Each event is matched by name, date and venue, and an email that fails to parse is kept for review.',
                    'Events, brands and artists can use their own web address (domain). The domain is checked with a DNS TXT record before an SSL certificate is set up.',
                    'Notifications are broadcast in real time over Reverb.',
                ],
            ],
        ],
        [
            'slug' => 'larder',
            'name' => 'Larder',
            'summary' => 'Household recipes and pantry app that imports recipes with Claude, matches them to stock and builds the shopping list.',
            'stack' => ['Laravel 13', 'PostgreSQL', 'Vue 3', 'Passkeys', 'Anthropic API'],
            'tests' => 1501,
            'tests_checked_at' => '2026-10-18',
            'url' => null,
            'visibility' => 'private beta',
            'status' => 'private',
            'detail' => [
                'problem' => 'Recipe apps ignore what is in the cupboards, and pantry apps fail because keeping them up to date is a chore.',
                'built' => 'An invite-only web app for a household, installed on the phone\'s Home Screen. It imports recipes from web pages, captions and photos with Claude, and from videos with Gemini, matches the ingredients to the pantry, and builds the shopping list from what is running low. Tapping "I cooked this" updates the stock, so the pantry stays accurate as a side effect of cooking. I built the Vue front end AI-first with Claude Code and reviewed every change, and the matching, offline sync and stock ledger are hand-designed.',
                'notes' => [
                    'Every AI import ends in a confirm-or-edit step. Claude answers against a JSON schema, so each result is validated before it becomes a draft.',
                    'Ingredient matching runs in PHP: exact alias, household alias, PostgreSQL trigram similarity, then Claude\'s suggestion. A correction is saved as a household alias, so the same ingredient never needs fixing twice.',
                    'The shopping list and quick stock changes work offline. Changes queue in IndexedDB and replay in order, with IDs made on the device so a replay is safe to repeat.',
                    'Stock changes are an append-only ledger, and undo writes a reversing entry. Use-by and best-before dates are stored and shown differently.',
                    'AI spend is recorded in pence from every response, with a daily import limit and a monthly cap for each person.',
                ],
            ],
        ],
        [
            'slug' => 'darkroom',
            'name' => 'Darkroom',
            'summary' => 'Image tools: background removal, upscaling, conversion, compression and image to PDF.',
            'stack' => ['Laravel 13', 'Inertia 3', 'Vue 3', 'Python FastAPI', 'pyvips', 'ONNX'],
            'tests' => 147,
            'tests_checked_at' => '2026-10-18',
            'url' => 'https://darkroom.gengar.me',
            'visibility' => null,
            'status' => 'live',
            'detail' => [
                'problem' => 'Online image tools upload your photos to someone else\'s server, and the popular ones charge a monthly fee or a per-image price for their full features. Darkroom does the same jobs on my own two-CPU server, which is shared with other sites, so heavy work has to be planned around that.',
                'built' => 'An invite-only Laravel app, for me and a few invited people, with fifteen server tools, including background removal, conversion, compression, face blur, OCR and image to PDF, plus upscaling and object removal in the browser. The Vue front end was built AI-first with Claude Code; I designed the split between server and browser work, and the Python service.',
                'notes' => [
                    'Light work and background removal run on the server, in a Python FastAPI service with pyvips and rembg that Laravel calls on localhost from a queued job.',
                    'Upscaling (Real-ESRGAN) and object removal (LaMa) run in the browser through onnxruntime-web, on WebGPU where the device has it, so those images never reach the server.',
                    'Each device downloads a model once and keeps it in the Cache API under its SHA-256, checked on every read. Cross-origin isolation headers are set only on those two tool pages.',
                    'Files are short-lived. An original is deleted when its result is first downloaded, and everything is pruned an hour after upload. EXIF and GPS data is always stripped.',
                    'Only MIT, Apache or BSD dependencies are allowed, plus libvips under the LGPL, so paid plans stay possible later.',
                ],
            ],
        ],
        [
            'slug' => 'drinksafe',
            'name' => 'DrinkSafe',
            'summary' => 'Community drink-spiking reports, venue safety scores and an incident heatmap.',
            'stack' => ['Laravel 13', 'Inertia 3', 'Vue 3', 'Leaflet', 'Turnstile'],
            'tests' => 449,
            'tests_checked_at' => '2026-10-18',
            'url' => 'https://drinksafe.app',
            'visibility' => null,
            'status' => 'live',
            'source' => 'https://github.com/MajorasJack/drinksafe.app',
            'detail' => [
                'problem' => 'People who have been spiked at a venue may want to warn others without going on record. DrinkSafe lets them report it anonymously, so others can make informed choices about where to go. It is informational, not a way to report a crime, and points people to the police.',
                'built' => 'Anonymous reports with no accounts, venue search, a safety score for each venue and an incident heatmap on Leaflet. Venues come from OpenStreetMap. It started as a React version and was rebuilt as a modular Laravel 13 app with Inertia and Vue. I used Claude Code AI-first for the front end and designed the scoring and privacy rules myself.',
                'notes' => [
                    'Reports are anonymous. Each one is checked for email addresses and phone numbers before it is saved, and submissions go through Cloudflare Turnstile.',
                    'A venue\'s safety score weights each report by age, halving every 90 days. Reports over a year old are left out, and a score needs at least three reports.',
                    'The heatmap groups reports into cells of about 100 metres and hides any cell with fewer than three reports, to protect privacy.',
                    'Records use UUID keys rather than counting IDs, for anonymity.',
                    'Pest 4 browser tests run alongside the feature and unit tests.',
                ],
            ],
        ],
        [
            'slug' => 'movies',
            'name' => 'Movies',
            'summary' => 'Catalogue for a physical film collection, filled in from a barcode scan.',
            'stack' => ['Laravel 13', 'Inertia 3', 'React 19', 'TMDB', 'OMDb'],
            'tests' => 2316,
            'tests_checked_at' => '2026-10-18',
            'url' => 'https://movies.gengar.me',
            'visibility' => null,
            'status' => 'live',
            'detail' => [
                'problem' => 'Mainstream film databases cover horror and cult releases on disc poorly, and no usable public API holds disc-level details. A barcode identifies an edition, not a film, and the catalogue has to model that.',
                'built' => 'A catalogue in three layers: the film, the physical release (label, region, cut and barcode) and my copy. A barcode scan in the phone browser looks the release up, and anything new goes in as a moderated contribution. I built the React front end AI-first with Claude Code; the three-layer data model and the provider chains are my design.',
                'notes' => [
                    'Film and barcode lookups run through ordered provider chains: TMDB, OMDb and Wikidata for films, and two retail barcode databases. A provider without a key drops out of the chain instead of failing every scan.',
                    'A scan that finds no barcode offers candidate releases matched by film title. Nothing is selected automatically, because a wrong match is invisible afterwards.',
                    'A box set is one release with several films attached, which needed no new tables.',
                    'Some shop feeds refuse the production server. Their catalogues are captured elsewhere and imported from the snapshot through the same code as a live import.',
                    'Duplicate editions are found and merged with tooling, and a merge can be reverted.',
                ],
            ],
        ],
    ],

    // Personal tools tied to Jack's own shops: no links, and no shop, store or supplier names (CONT-5).
    'smaller_tools' => [
        ['name' => 'Invoice Inbox', 'description' => 'Reads supplier invoices from Gmail with Claude and posts them to Xero.'],
        ['name' => 'Pre-orders Shopify app', 'description' => 'Pre-order and back-in-stock app with a storefront extension.'],
        ['name' => 'Shopify image optimiser', 'description' => 'Compresses product images and drafts alt text with Claude for a person to approve.'],
    ],

    // Names that must never appear on a public page (CONT-6). Entries are SHA-256 hashes of each normalised name, so the public repo does not list them; add one with `php artisan portfolio:denylist-hash "Name"`.
    'denylist' => [
        '25b6ae267dcdfdd1318176b0db5cb494a6c5156d378cea3bd5bccec582b6dd57',
        'd4a1d34e9688072e6971e4ef5541a7aea76e866b11c9bd0a56709f1c4f78f275',
        'ec523907ed47fcbd07c248a4afe4adf139e1583ea3adc22c9118d905af37e697',
        '86894d3b258af6db2d838db5f7652618f1aaa951e9a5920af989ce29eed562ec',
        'fd338981954ffc21aa5eb9dd79627782ae0e4b54eba595aeabf7303ffb03b8f4',
        '8bdc770db0c1d846eb52a72b75945f9047edf8e4f81433e79fc7be789b1feb18',
        'e9760f73ad19d8b13d30b4ff6c7e0a6d516517e513eeae912e76ace7afbc9685',
        'b9597898af1236caa29ad01317de021bd034082b22c8a70ed0362cc06ba71aeb',
        'c5de7d1e09535e2845bb315a766da1cfb5ea711fe43b8b8391d4e00c96c6b79e',
        '656d84e0dabc849f6124a80fb219b1cff2f1a252ad6866537927a9e8daf4fdf8',
        'ebf8aed8629d5b19cf3af0814c28ae278fb3cc7946700db793d95d4a4ba97717',
        '41fd1dbb96da5184ebc5fb9174c9297e01a056ac7814506b83e544c16876c4ef',
        'f55a85b2f2eb0790b99de47ae3e845fb3132aa8a892354ef4acb332a9473eeb9',
        '756b43f0fd64216e81ac3ed1e45dae0ba8afa51a5c1464244812588332b4323d',
        '8a4e7a37870eeafb3a6d09f9e987bb4b2b4f08e97bc4dcb89fba65c6f785a615',
        'fa49239287478e67ed1b492f9868ef52322d735d994925a66d6071e566ba621a',
        '578027d8eb97a008d2a4b26ac17f782ea93ed91dd2936e6d5e048fcb2d995530',
        '7af37550d5c99366bca0c153fa839bf4cf0a8dbfce550a3634b8d5fd7901a1e4',
        '27e996e51c8e94898d9897fb1ae884bf5548c3dfaee79b95af506b167e260696',
        '9ab9d868c868bcb37cc8f5b9b1a6759426ac9914f5e9c4b5b451946ce0c1cfa8',
        'adf829976c676ace912a99d504555b40528d88412299df855dfaeaf52c3cede1',
    ],
];
