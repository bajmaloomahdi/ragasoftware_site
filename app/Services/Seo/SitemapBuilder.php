<?php

namespace App\Services\Seo;

/**
 * Builds the dynamic XML sitemap from published content.
 * Full implementation in Phase 7 (SEO).
 */
class SitemapBuilder
{
    private const CACHE_KEY = 'seo.sitemap';

    public function build(): string
    {
        return cache()->remember(
            self::CACHE_KEY,
            now()->addMinutes((int) config('seo.sitemap.cache_minutes', 60)),
            fn () => $this->render(),
        );
    }

    public function forget(): void
    {
        cache()->forget(self::CACHE_KEY);
    }

    protected function render(): string
    {
        // Phase 7 replaces this with real, DB-driven URL discovery.
        $urls = [url('/')];

        $body = collect($urls)->map(fn ($u) => "  <url><loc>{$u}</loc></url>")->implode("\n");

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$body."\n"
            .'</urlset>';
    }
}
