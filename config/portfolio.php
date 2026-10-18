<?php

declare(strict_types=1);

/*
 * All site content (ADR-003). Each entry in `projects` becomes an
 * App\Portfolio\Project through Project::fromArray(), in this order.
 */

return [
    'available' => true,

    // Filled in S1-01 and S1-02.
    'profile' => [],
    'what_i_do' => [],

    // Keys per entry: slug, name, summary, stack (list), tests (int, approximate),
    // tests_checked_at (Y-m-d), url (nullable), visibility (label when url is null),
    // status (live or private), detail (problem, built, notes; S3). Filled in S1-03.
    'projects' => [],

    'smaller_tools' => [],

    // Names that must never appear on a public page (CONT-6). Filled in S1-05.
    'denylist' => [],
];
