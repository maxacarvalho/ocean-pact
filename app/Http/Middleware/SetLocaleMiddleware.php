<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request?->user()->locale
            ?? session()->get('locale')
            ?? $request->cookie('locale')
            ?? $this->getBrowserLanguage($request)
            ?? config('app.locale');

        if (array_key_exists($locale, $this->availableLocales())) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    private function getBrowserLanguage(Request $request): ?string
    {
        $browserLocales = preg_split('/[,;]/', $request->server('HTTP_ACCEPT_LANGUAGE'));

        foreach ($browserLocales as $browserLocale) {
            if (Arr::exists($this->availableLocales(), $browserLocale)) {
                return $browserLocale;
            }
        }

        return null;
    }

    private function availableLocales(): array
    {
        return config('ocean-pact.locales', []);
    }
}
