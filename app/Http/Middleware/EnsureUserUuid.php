<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserUuid
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uuid = $request->cookie('user_uuid');

        if (!$uuid) {
            $uuid = (string) Str::uuid();

            // Force the cookie into the current request so controllers/models can see it immediately
            $request->cookies->add(['user_uuid' => $uuid]);

            $response = $next($request);

            // Set cookie on the response for future requests
            return $response->withCookie(cookie()->forever('user_uuid', $uuid));
        }

        return $next($request);
    }
}
