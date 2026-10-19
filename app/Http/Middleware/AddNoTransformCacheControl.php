<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNoTransformCacheControl
{
    /**
     * Add `no-transform` to the response's Cache-Control header, keeping the
     * directives already there, so Cloudflare does not inject its bot detection
     * script into the page.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', sprintf('%s, no-transform', $response->headers->get('Cache-Control')));

        return $response;
    }
}
