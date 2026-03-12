<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class DefaultLocaleMiddleware
{
    /**
     * Apply a default locale for a route/group unless the user already chose a language.
     *
     * Usage: ->middleware('default.locale:tr')
     */
    public function handle(Request $request, Closure $next, string $locale = 'tr'): Response
    {
        $userSelected = (bool) session()->get('lang_user_selected', false);

        // Backward-compat: if session language differs from the system default,
        // treat it as an explicit choice (so we don't override it).
        if (!$userSelected && session()->has('lang')) {
            try {
                $userSelected = (string) session('lang') !== (string) getDefaultLanguage();
            } catch (\Throwable $e) {
                // Ignore; fall back to the explicit session flag only.
            }
        }

        if (!$userSelected) {
            App::setLocale($locale);

            // Keep session in sync so UI language toggles show the correct active state.
            session()->put('lang', $locale);

            // Both TR and EN are LTR, but keep it consistent for future languages.
            $direction = 'ltr';
            try {
                $direction = (string) (allLanguages()->where('code', $locale)->first()?->direction ?? 'ltr');
            } catch (\Throwable $e) {
                // Best-effort.
            }
            session()->put('text_direction', $direction);

            try {
                Carbon::setLocale($locale);
            } catch (\Throwable $e) {
                // Best-effort.
            }
        }

        return $next($request);
    }
}

