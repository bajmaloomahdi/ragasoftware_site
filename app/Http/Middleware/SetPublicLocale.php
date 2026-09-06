<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active locale for the public website.
 *
 * Today the site is Persian-only, so this always resolves to the app default
 * ("fa"). It is the single seam for future multi-language support: when an
 * "/en/..." prefix or a domain/subdomain strategy is added, only this class
 * (and route registration) changes — models already carry a `locale` column
 * and content queries are scoped through {@see \App\Support\HasLocale}.
 */
class SetPublicLocale
{
    /** Locales the site is allowed to serve. */
    public const SUPPORTED = ['fa'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        app()->setLocale($locale);
        $request->attributes->set('locale', $locale);

        return $next($request);
    }

    protected function resolve(Request $request): string
    {
        $segment = $request->segment(1);

        if ($segment && in_array($segment, self::SUPPORTED, true)) {
            return $segment;
        }

        return config('app.locale', 'fa');
    }
}
