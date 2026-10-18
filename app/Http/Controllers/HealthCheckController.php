<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Http\Response;

class HealthCheckController extends Controller
{
    /**
     * Report that the app boots, as plain text so the response loads nothing
     * from other hosts (the framework's own `/up` page pulls in CDN fonts and a script).
     */
    public function __invoke(): Response
    {
        event(new DiagnosingHealth);

        return response('ok', Response::HTTP_OK, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
