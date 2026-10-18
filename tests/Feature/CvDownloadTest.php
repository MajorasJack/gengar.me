<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Smalot\PdfParser\Parser;

it('serves the CV the header links to as a PDF', function (): void {
    expect(File::mimeType(public_path(config('portfolio.profile.cv'))))->toBe('application/pdf');
});

it('has no phone number in the CV text', function (): void {
    expect((new Parser)->parseFile(public_path(config('portfolio.profile.cv')))->getText())
        ->toContain(config('portfolio.profile.email'))
        ->not->toMatch('/\+?\d(?:[\s().-]*\d){9,}/');
});
