<?php

namespace App\Services\Seo;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Carbon;

/**
 * Dynamic XML sitemap built from published content only. Drafts and scheduled
 * (future-dated) items are excluded by the `published()` scope. Cached.
 */
class SitemapBuilder
{
    private const CACHE_KEY = 'seo.sitemap';

    public function build(): string
    {
        return cache()->remember(
            self::CACHE_KEY,
            now()->addMinutes((int) config('seo.sitemap.cache_minutes', 60)),
            fn () => $this->render($this->urls()),
        );
    }

    public function forget(): void
    {
        cache()->forget(self::CACHE_KEY);
    }

    /** @return array<int, array{loc: string, lastmod: ?string, priority: string, changefreq: string}> */
    public function urls(): array
    {
        $urls = [[
            'loc' => url('/'),
            'lastmod' => optional(Page::where('is_homepage', true)->value('updated_at'))?->toAtomString(),
            'priority' => '1.0',
            'changefreq' => 'weekly',
        ]];

        $include = config('seo.sitemap.include', []);

        if (in_array('pages', $include, true)) {
            foreach (Page::published()->where('is_homepage', false)->where('show_in_sitemap', true)->get(['slug', 'updated_at']) as $p) {
                $urls[] = $this->row(url('/'.$p->slug), $p->updated_at, '0.6');
            }
        }

        if (in_array('products', $include, true)) {
            $urls[] = $this->row(route('products.index'), null, '0.8', 'weekly');
            foreach (Product::published()->get(['slug', 'updated_at']) as $m) {
                $urls[] = $this->row(route('products.show', $m->slug), $m->updated_at, '0.8');
            }
        }

        if (in_array('services', $include, true)) {
            $urls[] = $this->row(route('services.index'), null, '0.7', 'weekly');
            foreach (Service::published()->get(['slug', 'updated_at']) as $m) {
                $urls[] = $this->row(route('services.show', $m->slug), $m->updated_at, '0.7');
            }
        }

        if (in_array('projects', $include, true)) {
            $urls[] = $this->row(route('projects.index'), null, '0.6', 'monthly');
            foreach (Project::published()->get(['slug', 'updated_at']) as $m) {
                $urls[] = $this->row(route('projects.show', $m->slug), $m->updated_at, '0.6');
            }
        }

        if (in_array('blog_posts', $include, true)) {
            $urls[] = $this->row(route('blog.index'), null, '0.7', 'daily');
            foreach (BlogPost::published()->get(['slug', 'updated_at']) as $m) {
                $urls[] = $this->row(route('blog.show', $m->slug), $m->updated_at, '0.6');
            }
        }

        if (in_array('blog_categories', $include, true)) {
            foreach (BlogCategory::query()->get(['slug']) as $c) {
                $urls[] = $this->row(route('blog.index', ['category' => $c->slug]), null, '0.4', 'weekly');
            }
        }

        return $urls;
    }

    private function row(string $loc, ?Carbon $lastmod, string $priority, string $changefreq = 'monthly'): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod?->toAtomString(),
            'priority' => $priority,
            'changefreq' => $changefreq,
        ];
    }

    private function render(array $urls): string
    {
        $body = collect($urls)->map(function (array $u) {
            $parts = ['  <url>', "    <loc>{$u['loc']}</loc>"];
            if ($u['lastmod']) {
                $parts[] = "    <lastmod>{$u['lastmod']}</lastmod>";
            }
            $parts[] = "    <changefreq>{$u['changefreq']}</changefreq>";
            $parts[] = "    <priority>{$u['priority']}</priority>";
            $parts[] = '  </url>';

            return implode("\n", $parts);
        })->implode("\n");

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$body."\n"
            .'</urlset>'."\n";
    }
}
