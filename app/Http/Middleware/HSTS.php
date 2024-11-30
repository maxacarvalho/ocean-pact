<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HSTS
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set(
            key: 'Strict-Transport-Security',
            values: 'max-age=31536000; includeSubDomains'
        );

        return $response;
    }
}
