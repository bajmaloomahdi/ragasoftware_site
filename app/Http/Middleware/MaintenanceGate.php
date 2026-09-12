<?php

namespace App\Http\Middleware;

use App\Services\Settings\SettingsRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public-site maintenance page, toggled from Settings → «حالت تعمیر و
 * نگهداری» (no deploy/SSH needed). The admin panel is always exempt so the
 * owner can keep managing content while it's on. A one-time visit to
 * /?preview=<bypass_token> sets a long-lived cookie that lets that browser
 * keep seeing the real site.
 */
class MaintenanceGate
{
    private const BYPASS_COOKIE = 'raga_preview';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*') || $request->is('up')) {
            return $next($request);
        }

        $settings = app(SettingsRepository::class);

        if (! $settings->get('maintenance.enabled', false)) {
            return $next($request);
        }

        $token = (string) $settings->get('maintenance.bypass_token', '');
        $signature = $token !== '' ? hash_hmac('sha256', $token, config('app.key')) : null;

        if ($signature && $request->query('preview') === $token) {
            return $next($request)->withCookie(
                cookie(self::BYPASS_COOKIE, $signature, 60 * 24 * 30)
            );
        }

        if ($signature && hash_equals($signature, (string) $request->cookie(self::BYPASS_COOKIE))) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'site' => $settings,
            'message' => $settings->get('maintenance.message', 'سایت در حال به‌روزرسانی می‌باشد.'),
        ], 503);
    }
}
