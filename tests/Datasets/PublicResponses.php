<?php

declare(strict_types=1);

dataset('public responses', [
    'homepage' => '/',
    'health check' => '/up',
    'sitemap' => '/sitemap.xml',
    'not found page' => fn (): string => sprintf('/%s', fake()->slug()),
]);
