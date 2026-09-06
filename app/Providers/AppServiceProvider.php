<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\Cms\MenuBuilder;
use App\Services\Settings\SettingsRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsRepository::class);
        $this->app->singleton(MenuBuilder::class);
    }

    public function boot(): void
    {
        // "super-admin" bypasses every gate check.
        Gate::before(fn ($user, $ability) => $user->hasRole('super-admin') ? true : null);

        // HTTPS everywhere once APP_URL says so (shared hosting behind a proxy/CDN).
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Every public Blade view gets the editable site settings, menus and
        // social links — so nothing on the site is hard-coded.
        View::composer(['layouts.*', 'partials.*', 'public.*', 'sections.*'], function ($view) {
            /** @var SettingsRepository $settings */
            $settings = app(SettingsRepository::class);
            /** @var MenuBuilder $menus */
            $menus = app(MenuBuilder::class);

            $view->with([
                'site' => $settings,
                'siteMenus' => $menus,
                'socialLinks' => $settings->socialLinks(),
            ]);
        });

        // Keep the settings cache warm-free of stale rows.
        SiteSetting::saved(fn () => app(SettingsRepository::class)->flush());
        SiteSetting::deleted(fn () => app(SettingsRepository::class)->flush());
    }
}
