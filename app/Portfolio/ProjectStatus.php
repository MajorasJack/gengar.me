<?php

declare(strict_types=1);

namespace App\Portfolio;

enum ProjectStatus: string
{
    case Live = 'live';
    case Private = 'private';
}
