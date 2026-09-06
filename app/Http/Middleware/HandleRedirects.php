<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies admin-managed 301/302 redirects (redirects table) before routing,
 * so renamed slugs keep their SEO value. Table is cached; the cache is
 * flushed by the Redirect model on save/delete.
 */
class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/'.ltrim($request->path(), '/');

        $map = cache()->rememberForever('site.redirects', function () {
            return Redirect::active()
                ->get(['id', 'from_path', 'to_path', 'status_code'])
                ->keyBy('from_path')
                ->toArray();
        });

        if (isset($map[$path])) {
            $rule = $map[$path];
            Redirect::whereKey($rule['id'])->increment('hits');

            $to = str_starts_with($rule['to_path'], 'http') ? $rule['to_path'] : url($rule['to_path']);

            return redirect($to, $rule['status_code']);
        }

        return $next($request);
    }
}
