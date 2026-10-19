<?php

declare(strict_types=1);

namespace App\Portfolio;

use App\Portfolio\Exceptions\InvalidProjectException;

final readonly class Project
{
    private const array REQUIRED_KEYS = ['slug', 'name', 'summary', 'stack', 'tests', 'tests_checked_at', 'status'];

    /**
     * @param  list<string>  $stack
     * @param  array{problem?: string, built?: string, notes?: list<string>}|null  $detail
     */
    public function __construct(
        public string $slug,
        public string $name,
        public string $summary,
        public array $stack,
        public int $tests,
        public string $testsCheckedAt,
        public ?string $url,
        public ?string $visibility,
        public ProjectStatus $status,
        public ?array $detail = null,
        public ?string $source = null,
    ) {}

    /**
     * Build a project from one entry of `portfolio.projects`, failing loudly so a
     * broken entry is caught in tests rather than on the live page.
     *
     * @param  array<string, mixed>  $entry
     *
     * @throws InvalidProjectException when a required key is missing, the status is unknown, there is neither a url nor a visibility label, or the url is not an https url with a host
     */
    public static function fromArray(array $entry): self
    {
        foreach (self::REQUIRED_KEYS as $key) {
            if (! array_key_exists($key, $entry)) {
                throw InvalidProjectException::missingKey(key: $key, slug: $entry['slug'] ?? '(no slug)');
            }
        }

        if (($entry['url'] ?? null) === null && ($entry['visibility'] ?? null) === null) {
            throw InvalidProjectException::missingUrlAndVisibility($entry['slug']);
        }

        if (($entry['url'] ?? null) !== null && (parse_url($entry['url'], PHP_URL_SCHEME) !== 'https' || ! parse_url($entry['url'], PHP_URL_HOST))) {
            throw InvalidProjectException::invalidUrl(slug: $entry['slug'], url: $entry['url']);
        }

        return new self(
            slug: $entry['slug'],
            name: $entry['name'],
            summary: $entry['summary'],
            stack: $entry['stack'],
            tests: $entry['tests'],
            testsCheckedAt: $entry['tests_checked_at'],
            url: $entry['url'] ?? null,
            visibility: $entry['visibility'] ?? null,
            status: ProjectStatus::tryFrom($entry['status'])
                ?? throw InvalidProjectException::unknownStatus(slug: $entry['slug'], status: $entry['status']),
            detail: $entry['detail'] ?? null,
            source: $entry['source'] ?? null,
        );
    }
}
