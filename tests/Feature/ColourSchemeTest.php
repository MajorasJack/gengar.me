<?php

declare(strict_types=1);

it('offers both colour schemes with dark first', function (string $uri): void {
    $this->get($uri)->assertSee('<meta name="color-scheme" content="dark light">', escape: false);
})->with([
    'homepage' => '/',
    'project page' => '/projects/promotr',
    'not found page' => fn (): string => sprintf('/%s', fake()->slug()),
]);
