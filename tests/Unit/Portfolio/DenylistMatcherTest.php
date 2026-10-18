<?php

declare(strict_types=1);

use App\Portfolio\DenylistMatcher;

it('normalises case, whitespace and punctuation', function (): void {
    $first = fake()->lexify('?????');
    $second = fake()->lexify('?????');

    expect(DenylistMatcher::normalise(sprintf("  %s \n &  %s. ", mb_strtoupper($first), $second)))
        ->toBe(sprintf('%s & %s', $first, $second));
});

it('hashes the normalised name with SHA-256', function (): void {
    $name = fake()->company();

    expect(DenylistMatcher::hash($name))->toBe(hash('sha256', DenylistMatcher::normalise($name)));
});

it('finds a company name in the copy', function (): void {
    $name = fake()->company();

    expect(new DenylistMatcher([DenylistMatcher::hash($name)])->matches(sprintf('<p>%s %s</p>', fake()->sentence(), e($name))))
        ->toBe([DenylistMatcher::hash($name)]);
});

it('finds a multi-word name containing an ampersand', function (): void {
    $name = sprintf('%s & %s', fake()->lastName(), fake()->lastName());

    expect(new DenylistMatcher([DenylistMatcher::hash($name)])->matches(sprintf('<p>Worked at %s.</p>', e($name))))
        ->toBe([DenylistMatcher::hash($name)]);
});

it('finds a four-word name containing an apostrophe', function (): void {
    $name = sprintf("%s O'%s %s %s", fake()->lexify('?????'), fake()->lexify('?????'), fake()->lexify('?????'), fake()->lexify('?????'));

    expect(new DenylistMatcher([DenylistMatcher::hash($name)])->matches(sprintf('<p>Worked at %s.</p>', e($name))))
        ->toBe([DenylistMatcher::hash($name)]);
});

it('finds a name split across tags', function (): void {
    $first = fake()->lexify('??????');
    $second = fake()->lexify('??????');

    expect(new DenylistMatcher([DenylistMatcher::hash(sprintf('%s %s', $first, $second))])->matches(sprintf('<td><strong>%s</strong></td><td>%s</td>', $first, $second)))
        ->toHaveCount(1);
});

it('finds an entity-encoded name', function (): void {
    $name = fake()->company();

    expect(new DenylistMatcher([DenylistMatcher::hash($name)])->matches(mb_encode_numericentity($name, [0x0, 0x10FFFF, 0, 0x10FFFF], 'UTF-8')))
        ->toBe([DenylistMatcher::hash($name)]);
});

it('finds a name inside an attribute', function (): void {
    $name = fake()->lexify('??????????');

    expect(new DenylistMatcher([DenylistMatcher::hash($name)])->matches(sprintf('<a href="https://%s.example">%s</a>', $name, fake()->word())))
        ->toBe([DenylistMatcher::hash($name)]);
});

it('ignores case', function (): void {
    $name = fake()->company();

    expect(new DenylistMatcher([DenylistMatcher::hash($name)])->matches(e(mb_strtoupper($name))))
        ->toBe([DenylistMatcher::hash($name)]);
});

it('does not match a name inside a longer word', function (): void {
    $name = fake()->lexify('??????');

    expect(new DenylistMatcher([DenylistMatcher::hash($name)])->matches(sprintf('<p>%sville %s</p>', $name, fake()->sentence())))
        ->toBeEmpty();
});
