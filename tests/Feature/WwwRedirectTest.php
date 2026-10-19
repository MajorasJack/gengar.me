<?php

declare(strict_types=1);

it('redirects the www root to the apex', function (): void {
    $this->get('https://www.gengar.me/')->assertMovedPermanently()->assertRedirect('https://gengar.me/');
});

it('keeps the path and query when redirecting www', function (): void {
    $path = fake()->slug();
    $query = http_build_query([fake()->word() => fake()->word()]);

    $this->get(sprintf('https://www.gengar.me/%s?%s', $path, $query))
        ->assertMovedPermanently()
        ->assertRedirect(sprintf('https://gengar.me/%s?%s', $path, $query));
});

it('redirects an unknown www path instead of returning not found', function (): void {
    $this->get(sprintf('https://www.gengar.me/%s', fake()->uuid()))->assertMovedPermanently();
});

it('does not redirect the apex', function (): void {
    $this->get('https://gengar.me/')->assertOk();
});

it('sets no cookies on the www redirect', function (): void {
    $this->get('https://www.gengar.me/')->assertHeaderMissing('Set-Cookie');
});

it('asks proxies not to transform the www redirect', function (): void {
    expect($this->get('https://www.gengar.me/')->headers->get('Cache-Control'))->toContain('no-transform');
});
