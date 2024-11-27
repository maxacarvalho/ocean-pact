<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HSTS
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $this->shouldAttachHeaders($response)) {
            return $next($request);
        }

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        return $next($request);
    }

    private function shouldAttachHeaders(Response $response): bool
    {
        return property_exists($response, 'exception') && ! $response->exception;
    }
}
