<?php

declare(strict_types=1);

dataset('public responses', [
    'homepage' => '/',
    'health check' => '/up',
    'not found page' => fn (): string => sprintf('/%s', fake()->slug()),
]);
