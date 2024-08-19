<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language');

        // Determine the preferred language
        if ($locale) {
            $preferredLocale = $this->parsePreferredLocale($locale);

            if ($preferredLocale) {
                App::setLocale($preferredLocale);
            }
        }

        return $next($request);
    }

    /**
     * Parse the Accept-Language header to extract the preferred locale.
     *
     * @param  string  $acceptLanguageHeader
     * @return string|null
     */
    private function parsePreferredLocale($acceptLanguageHeader)
    {
        // Parse the Accept-Language header to get the preferred locale
        $locales = explode(',', $acceptLanguageHeader);
        foreach ($locales as $locale) {
            $parts = explode(';', $locale);
            $language = trim($parts[0]);

            // Check if the language is supported in your application
            if (in_array($language, config('app.supported_locales'))) {
                return $language;
            }
        }

        return null;
    }
}
