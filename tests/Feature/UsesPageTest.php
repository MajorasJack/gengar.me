<?php

declare(strict_types=1);

it('renders the uses page', function (): void {
    $this->get('/uses')->assertOk()->assertViewIs('uses');
});

it('renders each section and item from config in order', function (): void {
    config(['portfolio.uses' => [
        ['title' => fake()->words(2, true), 'items' => [
            ['name' => fake()->words(2, true), 'note' => fake()->sentence()],
            ['name' => fake()->words(2, true), 'note' => fake()->sentence()],
        ]],
        ['title' => fake()->words(2, true), 'items' => [
            ['name' => fake()->words(2, true), 'note' => fake()->sentence()],
        ]],
    ]]);

    $this->get('/uses')->assertOk()->assertSeeInOrder([
        config('portfolio.uses.0.title'),
        config('portfolio.uses.0.items.0.name'),
        config('portfolio.uses.0.items.0.note'),
        config('portfolio.uses.0.items.1.name'),
        config('portfolio.uses.0.items.1.note'),
        config('portfolio.uses.1.title'),
        config('portfolio.uses.1.items.0.name'),
        config('portfolio.uses.1.items.0.note'),
    ]);
});

it('skips a section with no items', function (): void {
    $empty = sprintf('%s %s', fake()->word(), fake()->uuid());
    config(['portfolio.uses' => [
        ['title' => fake()->words(2, true), 'items' => [['name' => fake()->words(2, true), 'note' => fake()->sentence()]]],
        ['title' => $empty, 'items' => []],
    ]]);

    $this->get('/uses')->assertOk()->assertDontSee($empty);
});

it('titles the uses page', function (): void {
    $this->get('/uses')->assertOk()->assertSee('<title>Uses, Jack Evans</title>', escape: false);
});

it('describes the uses page', function (): void {
    $this->get('/uses')
        ->assertOk()
        ->assertSee('<meta name="description" content="The tools Jack Evans uses for PHP and Laravel development, and how gengar.me is built and hosted.">', escape: false);
});

it('points the uses page canonical URL at its own path', function (): void {
    $this->get('/uses')->assertOk()->assertSee('<link rel="canonical" href="https://gengar.me/uses">', escape: false);
});

it('describes Jack in JSON-LD on the uses page', function (): void {
    $this->get('/uses')->assertOk()->assertSee('<script type="application/ld+json">', escape: false);
});
