<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ForceLocaleMiddleware
{
    /**
     * Force a specific locale for a route/group.
     *
     * Usage: ->middleware('force.locale:en')
     */
    public function handle(Request $request, Closure $next, string $locale = 'en'): Response
    {
        App::setLocale($locale);

        // Keep Carbon locale in sync when localized formatting is used.
        try {
            Carbon::setLocale($locale);
        } catch (\Throwable $e) {
            // Best-effort; don't break requests if Carbon is not available.
        }

        return $next($request);
    }
}

