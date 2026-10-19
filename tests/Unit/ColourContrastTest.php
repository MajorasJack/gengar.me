<?php

declare(strict_types=1);

$stylesheet = file_get_contents(sprintf('%s/resources/css/app.css', dirname(__DIR__, 2)));

$tokens = function (string $block): array {
    preg_match_all('/--colour-([a-z-]+):\s*(#[0-9a-f]{6});/', $block, $matches);

    return array_combine($matches[1], $matches[2]);
};

preg_match('/(?:^|\n):root\s*\{([^}]*)\}/', $stylesheet, $darkBlock);
preg_match('/@media \(prefers-color-scheme: light\)\s*\{\s*:root\s*\{([^}]*)\}/', $stylesheet, $lightBlock);

$palettes = [
    'dark' => $tokens($darkBlock[1] ?? ''),
    'light' => $tokens($lightBlock[1] ?? ''),
];

$luminance = function (string $hex): float {
    [$red, $green, $blue] = array_map(
        fn (int $channel): float => $channel / 255 <= 0.04045 ? $channel / 255 / 12.92 : (($channel / 255 + 0.055) / 1.055) ** 2.4,
        sscanf($hex, '#%02x%02x%02x'),
    );

    return 0.2126 * $red + 0.7152 * $green + 0.0722 * $blue;
};

test('the light palette overrides every dark colour token', function () use ($palettes): void {
    expect(array_keys($palettes['light']))->toEqualCanonicalizing(array_keys($palettes['dark']))->not->toBeEmpty();
});

test('the colour pair meets WCAG 2.2 AA contrast', function (string $palette, string $foreground, string $background, float $minimum) use ($palettes, $luminance): void {
    $colours = $palettes[$palette];
    $lighter = max($luminance($colours[$foreground]), $luminance($colours[$background]));
    $darker = min($luminance($colours[$foreground]), $luminance($colours[$background]));

    expect(($lighter + 0.05) / ($darker + 0.05))->toBeGreaterThanOrEqual($minimum);
})->with(['dark', 'light'])->with([
    'ink text on bg' => ['ink', 'bg', 4.5],
    'ink-soft text on bg' => ['ink-soft', 'bg', 4.5],
    'muted text on bg' => ['muted', 'bg', 4.5],
    'accent text on bg' => ['accent', 'bg', 4.5],
    'accent skip link on raise' => ['accent', 'raise', 4.5],
    'accent focus ring on bg' => ['accent', 'bg', 3.0],
    'accent focus ring on raise' => ['accent', 'raise', 3.0],
    'ok status dot on bg' => ['ok', 'bg', 3.0],
    'muted status dot on bg' => ['muted', 'bg', 3.0],
]);

test('the stylesheet offers both colour schemes with dark first', function () use ($stylesheet): void {
    expect($stylesheet)->toMatch('/(?:^|\n):root\s*\{\s*color-scheme: dark light;/');
});

test('every link shows a focus ring', function () use ($stylesheet): void {
    expect($stylesheet)->toMatch('/(^|\n)a:focus-visible\s*\{[^}]*outline:\s*2px solid var\(--colour-accent\);/');
});

test('no rule removes the focus outline', function () use ($stylesheet): void {
    expect($stylesheet)->not->toMatch('/outline(-style|-width)?:\s*(none|0)\b/');
});

test('every colour token the stylesheet uses is defined in both palettes', function () use ($stylesheet, $palettes): void {
    preg_match_all('/var\(--colour-([a-z-]+)\)/', $stylesheet, $used);

    expect(array_values(array_diff(array_unique($used[1]), array_keys($palettes['dark']))))->toBe([])
        ->and(array_values(array_diff(array_unique($used[1]), array_keys($palettes['light']))))->toBe([]);
});

test('no colour is set outside the palette tokens', function () use ($stylesheet): void {
    expect(preg_replace('/^\s*--colour-[a-z-]+:\s*#[0-9a-f]{6};$/m', '', $stylesheet))
        ->not->toMatch('/#[0-9a-fA-F]{3,8}\b|\b(?:rgba?|hsla?)\(/');
});
