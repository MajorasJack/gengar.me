<?php

declare(strict_types=1);

it('prints the hash of the lowercased, trimmed name', function (): void {
    $name = fake()->lexify('????????');

    $this->artisan('portfolio:denylist-hash', ['name' => sprintf('  %s  ', mb_strtoupper($name))])
        ->expectsOutput(hash('sha256', $name))
        ->assertSuccessful();
});

it('refuses a name longer than the matcher can find', function (): void {
    $this->artisan('portfolio:denylist-hash', ['name' => fake()->words(5, true)])
        ->assertFailed();
});
