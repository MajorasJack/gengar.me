<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNoTransformCacheControl
{
    /**
     * Add `no-transform` to the response's Cache-Control header so Cloudflare
     * does not inject its bot detection script into the page.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Store the computed default (`no-cache, private`) as directives, or adding one replaces it.
        $response->headers->set('Cache-Control', $response->headers->get('Cache-Control'));
        $response->headers->addCacheControlDirective('no-transform');

        return $response;
    }
}
