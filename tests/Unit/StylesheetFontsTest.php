<?php

declare(strict_types=1);

$stylesheet = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

test('every font face in the stylesheet is self-hosted', function () use ($stylesheet): void {
    preg_match_all('/url\([\'"]?([^\'")]+)[\'"]?\)/', $stylesheet, $matches);

    expect($matches[1])->not->toBeEmpty()
        ->each->toStartWith('/fonts/')
        ->and(array_filter($matches[1], fn (string $path): bool => ! is_file(dirname(__DIR__, 2).'/public'.$path)))->toBeEmpty();
});

test('every font face swaps in once loaded', function () use ($stylesheet): void {
    expect(substr_count($stylesheet, '@font-face'))
        ->toBeGreaterThan(0)
        ->toBe(substr_count($stylesheet, 'font-display: swap'));
});
