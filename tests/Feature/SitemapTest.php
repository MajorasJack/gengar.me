<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

it('serves the sitemap as XML', function (): void {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
});

it('lists the homepage at the canonical host', function (): void {
    $this->get('/sitemap.xml')
        ->assertSee(sprintf('<loc>https://%s/</loc>', config('portfolio.canonical_host')), escape: false);
});

it('lists only pages the site serves on the canonical host', function (): void {
    $locations = collect(simplexml_load_string($this->get('/sitemap.xml')->getContent())->url)
        ->map(fn (SimpleXMLElement $url): string => (string) $url->loc);

    $locations->each(function (string $location): void {
        expect($location)->toStartWith(sprintf('https://%s/', config('portfolio.canonical_host')));
        $this->get(parse_url($location, PHP_URL_PATH))->assertOk();
    });
});

it('lists each project detail page', function (): void {
    $response = $this->get('/sitemap.xml');

    collect(config('portfolio.projects'))->each(fn (array $project) => $response->assertSee(
        sprintf('<loc>https://%s/projects/%s</loc>', config('portfolio.canonical_host'), $project['slug']),
        escape: false,
    ));
})->skip(fn (): bool => ! Route::has('projects.show'), 'S3-01 adds the projects.show detail route');

it('points robots.txt at the sitemap', function (): void {
    expect(File::get(public_path('robots.txt')))
        ->toContain(sprintf('Sitemap: https://%s/sitemap.xml', config('portfolio.canonical_host')));
});
