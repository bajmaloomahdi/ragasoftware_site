<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Draft / Scheduled / Published lifecycle.
 *
 * Public queries must use ->published(). Content is considered live when it is
 * "published" (or "scheduled" with a past date — so the site is correct even if
 * the `content:publish-scheduled` cron is late) AND published_at has passed.
 */
trait HasPublishing
{
    public static function bootHasPublishing(): void
    {
        static::saving(function (Model $model) {
            if ($model->status === 'published' && blank($model->published_at)) {
                $model->published_at = now();
            }
        });

        // Any change to publishable content can affect the sitemap.
        $flush = fn () => cache()->forget('seo.sitemap');
        static::saved($flush);
        static::deleted($flush);
    }

    public function initializeHasPublishing(): void
    {
        $this->mergeCasts(['published_at' => 'datetime']);

        if (! isset($this->attributes['status'])) {
            $this->attributes['status'] = 'draft';
        }
    }

    public function scopePublished(Builder $query): void
    {
        $query
            ->where(function (Builder $q) {
                $q->where('status', 'published')
                    ->orWhere(function (Builder $q) {
                        $q->where('status', 'scheduled')->whereNotNull('published_at');
                    });
            })
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): void
    {
        $query->where('status', 'draft');
    }

    public function scopeStatus(Builder $query, ?string $status): void
    {
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
    }

    public function getIsLiveAttribute(): bool
    {
        return in_array($this->status, ['published', 'scheduled'], true)
            && $this->published_at !== null
            && $this->published_at->isPast();
    }
}
