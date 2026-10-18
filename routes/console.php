<?php

declare(strict_types=1);

use App\Portfolio\DenylistMatcher;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('portfolio:denylist-hash {name}', function (string $name): int {
    if (count(explode(' ', DenylistMatcher::normalise($name))) > DenylistMatcher::MAX_WORDS) {
        $this->error(sprintf('Names longer than %d words are never matched.', DenylistMatcher::MAX_WORDS));

        return Command::FAILURE;
    }

    $this->line(DenylistMatcher::hash($name));

    return Command::SUCCESS;
})->purpose('Print the portfolio.denylist entry for a name');
