<?php

declare(strict_types=1);

it('shows no denylisted name', function (string $uri): void {
    expect(html_entity_decode($this->get($uri)->getContent()))
        ->not->toMatch(sprintf('/\b(?:%s)\b/iu', collect(config('portfolio.denylist'))->map(fn (string $name): string => preg_quote($name, '/'))->implode('|')));
})->with('public responses');

it('shows no performance figure', function (string $uri): void {
    expect(html_entity_decode($this->get($uri)->getContent()))
        ->not->toMatch('/\d+(\.\d+)?s\s*→\s*\d/u');
})->with('public responses');
