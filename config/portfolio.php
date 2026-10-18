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

    // Filled in S1-02.
    'what_i_do' => [],

    // Keys per entry: slug, name, summary, stack (list), tests (int, approximate),
    // tests_checked_at (Y-m-d), url (nullable), visibility (label when url is null),
    // status (live or private), detail (problem, built, notes; S3). Filled in S1-03.
    'projects' => [],

    'smaller_tools' => [],

    // Names that must never appear on a public page (CONT-6). Filled in S1-05.
    'denylist' => [],
];
