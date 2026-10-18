<?php

declare(strict_types=1);

$stylesheet = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

preg_match_all('/--colour-([a-z-]+):\s*(#[0-9a-f]{6});/', $stylesheet, $matches);
$colours = array_combine($matches[1], $matches[2]);

$luminance = function (string $hex): float {
    [$red, $green, $blue] = array_map(
        fn (int $channel): float => $channel / 255 <= 0.04045 ? $channel / 255 / 12.92 : (($channel / 255 + 0.055) / 1.055) ** 2.4,
        sscanf($hex, '#%02x%02x%02x'),
    );

    return 0.2126 * $red + 0.7152 * $green + 0.0722 * $blue;
};

test('the colour pair meets WCAG 2.2 AA contrast', function (string $foreground, string $background, float $minimum) use ($colours, $luminance): void {
    $lighter = max($luminance($colours[$foreground]), $luminance($colours[$background]));
    $darker = min($luminance($colours[$foreground]), $luminance($colours[$background]));

    expect(($lighter + 0.05) / ($darker + 0.05))->toBeGreaterThanOrEqual($minimum);
})->with([
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

test('every link shows a focus ring', function () use ($stylesheet): void {
    expect($stylesheet)->toMatch('/(^|\n)a:focus-visible\s*\{[^}]*outline:\s*2px solid var\(--colour-accent\);/');
});

test('no rule removes the focus outline', function () use ($stylesheet): void {
    expect($stylesheet)->not->toMatch('/outline(-style|-width)?:\s*(none|0)\b/');
});
