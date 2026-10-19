<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectWwwToCanonicalHost
{
    /**
     * Redirect requests on the `www.` form of the canonical host to the canonical
     * host, so the site has a single address.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $canonicalHost = config()->string('portfolio.canonical_host');

        if ($request->getHost() !== sprintf('www.%s', $canonicalHost)) {
            return $next($request);
        }

        return new RedirectResponse(sprintf('https://%s%s', $canonicalHost, $request->getRequestUri()), Response::HTTP_MOVED_PERMANENTLY);
    }
}
