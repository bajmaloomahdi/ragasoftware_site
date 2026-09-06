<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Settings\SettingsRepository;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(SettingsRepository $settings): Response
    {
        $lines = ['User-agent: *'];

        foreach (config('seo.robots.disallow', []) as $path) {
            $lines[] = "Disallow: {$path}";
        }

        if ($extra = trim((string) $settings->get('seo.robots_extra', ''))) {
            $lines[] = $extra;
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
