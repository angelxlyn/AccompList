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
            Cookie::queue('user_uuid', $uuid, 60 * 24 * 365); // 1 year
        }

        // Always store in attributes so it's accessible via request()->get('user_uuid')
        $request->attributes->add(['user_uuid' => $uuid]);

        return $next($request);
    }
}
