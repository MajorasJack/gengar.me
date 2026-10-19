<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('links the icon with a root-relative path', function (string $tag): void {
    $this->get('/')->assertOk()->assertSee($tag, escape: false);
})->with([
    'svg favicon' => '<link rel="icon" href="/favicon.svg" type="image/svg+xml">',
    'ico fallback' => '<link rel="icon" href="/favicon.ico" sizes="32x32">',
    'apple touch icon' => '<link rel="apple-touch-icon" href="/apple-touch-icon.png">',
]);

it('ships the icon file the layout links to', function (string $path, string $mimeType): void {
    expect(File::mimeType(public_path($path)))->toBe($mimeType);
})->with([
    'svg favicon' => ['favicon.svg', 'image/svg+xml'],
    'ico fallback' => ['favicon.ico', 'image/vnd.microsoft.icon'],
    'apple touch icon' => ['apple-touch-icon.png', 'image/png'],
]);
