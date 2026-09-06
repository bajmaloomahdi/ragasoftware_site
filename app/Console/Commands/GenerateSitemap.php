<?php

namespace App\Console\Commands;

use App\Services\Seo\SitemapBuilder;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Rebuild and cache the XML sitemap from published content.';

    public function handle(SitemapBuilder $builder): int
    {
        $builder->forget();
        $count = substr_count($builder->build(), '<loc>');

        $this->info("نقشه سایت با {$count} نشانی بازسازی شد.");

        return self::SUCCESS;
    }
}
