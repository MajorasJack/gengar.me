<?php

declare(strict_types=1);

/*
 * All site content (ADR-003). Each entry in `projects` becomes an
 * App\Portfolio\Project through Project::fromArray(), in this order.
 */

return [
    'available' => true,

    'profile' => [
        'name' => 'Jack Evans',
        'role' => 'senior PHP / Laravel engineer',
        'location' => 'Manchester, UK',
        'remote' => 'remote-first',
        // TODO(owner): S1-02 replaces this draft from mockup C with Jack's intro (70 words at most).
        'intro' => 'Fourteen years building APIs, integrations and the databases underneath them, in healthcare SaaS, food delivery, legal tech and e-commerce. I like owning a problem end to end, from the design doc to the measured result.',
        'email' => 'jack@gengar.me',
        'linkedin' => 'https://www.linkedin.com/in/jack-evans-2526a1a3',
        'github' => 'https://github.com/MajorasJack',
        'cv' => '/cv.pdf',
    ],

    // TODO(owner): S1-02 replaces these drafts from mockup C with Jack's copy: one sentence each,
    // no employer names, no performance figures (CONT-2).
    'what_i_do' => [
        ['title' => 'Performance', 'description' => 'Slow queries traced to the cause and fixed at the data layer.'],
        ['title' => 'APIs and integrations', 'description' => 'Queues, webhooks, third-party platforms, eID sign-in.'],
        ['title' => 'Standards', 'description' => 'Testing guides, review norms, CI, RFCs.'],
        ['title' => 'Claude Code', 'description' => 'AI-led front ends. I own the backend, tests and review.'],
    ],

    // Keys per entry: slug, name, summary, stack (list), tests (int, approximate),
    // tests_checked_at (Y-m-d), url (nullable), visibility (label when url is null),
    // status (live or private), detail (problem, built, notes; S3). Test counts are the
    // PHP suite as listed by `pest --list-tests`.
    'projects' => [
        [
            'slug' => 'promotr',
            'name' => 'Promotr',
            'summary' => 'Event management for live-music promoters: budgets, tours, artists, contracts and ticketing.',
            'stack' => ['Laravel 12', 'Inertia 2', 'Vue 3', 'Reverb', 'Meilisearch', 'Cashier'],
            'tests' => 30945,
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
            'tests' => 1741,
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
            'tests' => 165,
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
            'tests' => 512,
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
            'tests' => 2423,
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

    // Names that must never appear on a public page (CONT-6). Filled in S1-05.
    'denylist' => [],
];
