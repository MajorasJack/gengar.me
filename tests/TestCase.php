<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * CI does not build assets, so render @vite as nothing instead of failing on a missing manifest.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
