<?php

declare(strict_types=1);

namespace App\Portfolio\Exceptions;

use InvalidArgumentException;

class InvalidProjectException extends InvalidArgumentException
{
    /**
     * A project entry in config/portfolio.php is missing a key the site needs.
     */
    public static function missingKey(string $key, string $slug): self
    {
        return new self(sprintf('Portfolio project "%s" is missing the required key "%s".', $slug, $key));
    }

    /**
     * A project with no public URL has nothing to show in place of its host.
     */
    public static function missingUrlAndVisibility(string $slug): self
    {
        return new self(sprintf('Portfolio project "%s" needs a url or a visibility label.', $slug));
    }

    /**
     * A project's status is not one the page knows how to show.
     */
    public static function unknownStatus(string $slug, string $status): self
    {
        return new self(sprintf('Portfolio project "%s" has an unknown status "%s".', $slug, $status));
    }
}
