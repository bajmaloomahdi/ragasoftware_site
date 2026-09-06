<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Console\Command;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled';

    protected $description = 'Promote scheduled content whose publish time has passed to "published".';

    public function handle(): int
    {
        $total = 0;

        foreach ([Page::class, Product::class, Service::class, Project::class, BlogPost::class] as $model) {
            $count = $model::query()
                ->where('status', 'scheduled')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->update(['status' => 'published']);

            $total += $count;
        }

        if ($total > 0) {
            cache()->forget('seo.sitemap');
            $this->info("{$total} مورد منتشر شد.");
        }

        return self::SUCCESS;
    }
}
