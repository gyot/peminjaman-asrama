<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowIframe
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $response = $next($request);

        $response->headers->set('X-Frame-Options', 'ALLOWALL');
        // Optional, untuk CORS lebih lanjut
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
