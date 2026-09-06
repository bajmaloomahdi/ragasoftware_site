<?php

namespace App\Models;

use App\Models\Concerns\HasLocale;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSeoMeta;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Mediable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasLocale, HasPublishing, HasSeoMeta, Mediable;

    protected $fillable = [
        'locale', 'translation_group', 'title', 'slug', 'summary', 'body', 'icon',
        'media_id', 'featured', 'status', 'published_at', 'sort_order', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function features(): MorphMany
    {
        return $this->morphMany(Feature::class, 'featureable')->where('kind', 'feature')->orderBy('sort_order');
    }

    public function publicPath(?string $slug = null): string
    {
        return '/services/'.($slug ?? $this->slug);
    }

    public function scopeFeatured(Builder $q): void
    {
        $q->where('featured', true);
    }

    public function scopeOrdered(Builder $q): void
    {
        $q->orderBy('sort_order')->orderByDesc('published_at');
    }
}
