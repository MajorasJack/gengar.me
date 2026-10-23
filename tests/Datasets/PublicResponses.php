<?php

declare(strict_types=1);

dataset('public responses', [
    'homepage' => '/',
    'health check' => '/up',
    'sitemap' => '/sitemap.xml',
    'project page' => '/projects/promotr',
    'private project page' => '/projects/larder',
    'invoice inbox page' => '/projects/invoice-inbox',
    'uses page' => '/uses',
    'not found page' => fn (): string => sprintf('/%s', fake()->slug()),
]);
