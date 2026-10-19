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
    // status (live or private), detail (problem, built, notes; S3). tests is the number of
    // test functions in the PHP suite, from `pest --list-tests` with dataset rows collapsed.
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
