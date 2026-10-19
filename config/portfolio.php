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
    // status (live or private), detail (overview, problem, built, notes; S3), source (public repo url,
    // optional), screenshot (src, alt, width, height of a WebP in public/images/projects; optional).
    // tests is the number of test functions in the PHP suite, from `pest --list-tests` with dataset rows collapsed.
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
            'screenshot' => [
                'src' => '/images/projects/promotr.webp',
                'alt' => 'Promotr\'s Go Live dialog: a publishing readiness checklist with tickets, artists, venue and status all ticked, above the event\'s own promotr.co web address.',
                'width' => 1600,
                'height' => 900,
            ],
            'detail' => [
                'overview' => 'Promotr is an event management platform for live-music promoters, booking agents, venues and artists. It brings event and tour budgets, artist bookings, contracts, tasks and ticket sales into one place, with public pages for events, brands and artists.',
                'problem' => 'Promoters run shows from spreadsheets and separate tools, and an event\'s budget keeps moving until the night itself.',
                'built' => 'A modular, multi-tenant Laravel 12 app with an Inertia and Vue 3 front end, built AI-first with Claude Code. Search runs on Meilisearch, billing on Cashier and live updates on Reverb.',
                'notes' => [
                    'A modular codebase of over 50 modules, each with its own tests.',
                    'Each organisation\'s data is kept separate, with role-based access for its team.',
                    'Ticket sales come in from outside ticketing platforms, so a budget tracks real revenue.',
                    'Events, brands and artists can be published on their own web address (domain).',
                    'Notifications arrive in real time over WebSockets.',
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
                'overview' => 'Larder is a recipe and pantry app for a household, used on the phone from the Home Screen. It saves recipes from websites, social media, photos and videos, keeps track of what is in the fridge and cupboards, suggests what to cook before food expires, and builds the shopping list from what is running low. It is in private beta, by invite.',
                'problem' => 'Recipe apps ignore what is in the cupboards, and pantry apps fail because keeping them up to date is a chore.',
                'built' => 'A modular Laravel 13 API on PostgreSQL with a Vue 3 single-page app, installed as a PWA and built AI-first with Claude Code. Recipe imports use Claude and video imports use Gemini. Sign-in is passwordless, with passkeys first.',
                'notes' => [
                    'AI imports always end in a step where a person confirms or edits the result.',
                    'Cooking a recipe updates the stock, so the pantry stays accurate without extra upkeep.',
                    'The shopping list and quick stock changes work offline on a phone and sync when the signal returns.',
                    'Ingredients are matched to the pantry automatically, and a correction is remembered for next time.',
                    'Use-by and best-before dates are kept apart and never shown the same way.',
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
                'overview' => 'Darkroom is a set of image tools in one invite-only web app. It removes backgrounds and objects, upscales photos, converts and compresses images, blurs faces, reads text with OCR and turns images into PDFs.',
                'problem' => 'Online image tools upload people\'s photos to someone else\'s server, and the popular ones charge for their full features.',
                'built' => 'A Laravel 13 app with an Inertia and Vue 3 front end, built AI-first with Claude Code, and a Python FastAPI service that does the image processing with pyvips and ONNX models.',
                'notes' => [
                    'Image processing is split between a server service and the browser.',
                    'Upscaling and object removal run in the browser, on WebGPU where the device has it, so those photos never leave the device.',
                    'Location and camera details are removed from every result.',
                    'Uploads are kept only as long as the job needs them.',
                    'A batch of files comes back as a single download.',
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
            'screenshot' => [
                'src' => '/images/projects/drinksafe.webp',
                'alt' => 'The DrinkSafe homepage, headed "Community awareness for safer nights out", with a venue search for Manchester and links to browse the map or share a report.',
                'width' => 1600,
                'height' => 900,
            ],
            'detail' => [
                'overview' => 'DrinkSafe is a public website where people can report, anonymously, a venue where they were spiked. Anyone can search venues, see each venue\'s safety score and reports, and view an incident heatmap, with links to the police throughout.',
                'problem' => 'People who have been spiked at a venue may want to warn others without going on record. DrinkSafe is informational, not a way to report a crime.',
                'built' => 'A modular Laravel 13 app with Inertia and Vue 3, rebuilt from an earlier React version, with the front end built AI-first with Claude Code. Maps use Leaflet, and venues come from OpenStreetMap.',
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
            'slug' => 'rewind',
            'name' => 'Rewind',
            'previous_slugs' => ['movies'],
            'summary' => 'Catalogue for a physical film collection, filled in from a barcode scan.',
            'stack' => ['Laravel 13', 'Inertia 3', 'React 19', 'TMDB', 'OMDb'],
            'tests' => 2316,
            'tests_checked_at' => '2026-10-18',
            'url' => 'https://movies.gengar.me',
            'visibility' => null,
            'status' => 'live',
            'detail' => [
                'overview' => 'Rewind is a catalogue for a physical film collection: DVD, Blu-ray, 4K Ultra HD, VHS, LaserDisc and other disc and tape formats. Scanning a barcode on a phone adds an edition to the collection, and a wantlist tracks what is still to find. The catalogue of films and editions is shared, and signed-in members can add to it.',
                'problem' => 'Film databases list films, not the editions on a shelf, and a barcode identifies an edition rather than a film.',
                'built' => 'A modular Laravel 13 app on PostgreSQL with an Inertia and React 19 front end, built AI-first with Claude Code. Film details come from TMDB and OMDb.',
                'notes' => [
                    'Films and their physical editions are recorded separately, so a re-release, a special edition and a box set each have their own entry.',
                    'Barcode scanning works in the phone browser, with no app to install.',
                    'Film and barcode lookups try several sources in turn, and scanning keeps working when one is unavailable.',
                    'New editions are checked before they appear in the shared catalogue, and duplicate editions can be merged and the merge undone.',
                    'Each copy records its condition, from sealed to damaged.',
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
