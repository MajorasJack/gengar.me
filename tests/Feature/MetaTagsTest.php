<?php

declare(strict_types=1);

use App\Portfolio\DenylistMatcher;
use Illuminate\Support\Str;

it('titles the homepage with the name and role', function (): void {
    config(['portfolio.profile.name' => fake()->name(), 'portfolio.profile.role' => fake()->jobTitle()]);

    $this->get('/')->assertOk()->assertSee(sprintf('<title>%s, %s</title>', e(config('portfolio.profile.name')), e(config('portfolio.profile.role'))), escape: false);
});

it('describes the homepage from config', function (): void {
    config(['portfolio.profile.description' => fake()->sentence()]);

    $this->get('/')->assertOk()->assertSee(sprintf('<meta name="description" content="%s">', e(config('portfolio.profile.description'))), escape: false);
});

it('points the canonical URL at the canonical host whatever the app URL', function (): void {
    config(['app.url' => fake()->url(), 'portfolio.canonical_host' => fake()->domainName()]);

    $this->get(sprintf('/?%s=%s', fake()->word(), fake()->word()))
        ->assertOk()
        ->assertSee(sprintf('<link rel="canonical" href="https://%s/">', config('portfolio.canonical_host')), escape: false);
});

it('renders the Open Graph and Twitter card tags', function (string $tag): void {
    $this->get('/')->assertOk()->assertSee($tag, escape: false);
})->with([
    'type' => '<meta property="og:type" content="website">',
    'site name' => '<meta property="og:site_name" content="gengar.me">',
    'title' => '<meta property="og:title" content="Jack Evans, senior PHP / Laravel engineer">',
    'url' => '<meta property="og:url" content="https://gengar.me/">',
    'image' => '<meta property="og:image" content="https://gengar.me/og.png">',
    'image width' => '<meta property="og:image:width" content="1200">',
    'image height' => '<meta property="og:image:height" content="630">',
    'image alt' => '<meta property="og:image:alt" content="Jack Evans, senior PHP / Laravel engineer">',
    'twitter card' => '<meta name="twitter:card" content="summary_large_image">',
]);

it('repeats the meta description as the Open Graph description', function (): void {
    config(['portfolio.profile.description' => fake()->sentence()]);

    $this->get('/')->assertOk()->assertSee(sprintf('<meta property="og:description" content="%s">', e(config('portfolio.profile.description'))), escape: false);
});

it('titles a project page with the project and owner names', function (): void {
    $this->get('/projects/promotr')->assertOk()->assertSee('<title>Promotr, Jack Evans</title>', escape: false);
});

it('describes a project page with its summary', function (): void {
    $this->get('/projects/promotr')
        ->assertOk()
        ->assertSee(sprintf('<meta name="description" content="%s">', e(config('portfolio.projects.0.summary'))), escape: false);
});

it('points a project page canonical URL at its own path', function (): void {
    $this->get('/projects/promotr')->assertOk()->assertSee('<link rel="canonical" href="https://gengar.me/projects/promotr">', escape: false);
});

it('ships a 1200 by 630 PNG as the Open Graph image', function (): void {
    expect(getimagesize(public_path('og.png')))->toMatchArray([0 => 1200, 1 => 630, 'mime' => 'image/png']);
});

it('describes Jack as a Person in JSON-LD with no phone or employer', function (): void {
    $json = Str::between($this->get('/')->assertOk()->getContent(), '<script type="application/ld+json">', '</script>');

    expect(json_decode($json, true, flags: JSON_THROW_ON_ERROR))->toBe([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => 'Jack Evans',
        'jobTitle' => 'Senior PHP / Laravel engineer',
        'url' => 'https://gengar.me/',
        'sameAs' => [
            'https://www.linkedin.com/in/jack-evans-2526a1a3',
            'https://github.com/MajorasJack',
        ],
    ]);
});

it('runs the meta description through the denylist check', function (): void {
    $company = fake()->company();
    config([
        'portfolio.profile.description' => sprintf('Formerly at %s.', $company),
        'portfolio.denylist' => [DenylistMatcher::hash($company)],
    ]);

    expect(new DenylistMatcher(config('portfolio.denylist'))->matches($this->get('/')->getContent()))
        ->toBe([DenylistMatcher::hash($company)]);
});

it('titles the not found page', function (): void {
    $this->get(sprintf('/%s', fake()->slug()))->assertNotFound()->assertSee('<title>Page not found, Jack Evans</title>', escape: false);
});

it('describes the not found page', function (): void {
    $this->get(sprintf('/%s', fake()->slug()))->assertNotFound()->assertSee('<meta name="description" content="', escape: false);
});
