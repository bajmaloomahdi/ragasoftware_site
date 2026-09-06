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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasLocale, HasPublishing, HasSeoMeta, Mediable;

    protected $fillable = [
        'locale', 'translation_group', 'category_id', 'title', 'slug', 'tagline',
        'summary', 'body', 'icon', 'hero_media_id', 'featured', 'cta_label', 'cta_url',
        'status', 'published_at', 'sort_order', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function heroImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_media_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(ProductFeature::class)->orderBy('sort_order');
    }

    public function activeFeatures(): HasMany
    {
        return $this->features()->where('is_active', true);
    }

    public function advantages(): MorphMany
    {
        return $this->morphMany(Feature::class, 'featureable')->where('kind', 'advantage')->orderBy('sort_order');
    }

    public function publicPath(?string $slug = null): string
    {
        return '/products/'.($slug ?? $this->slug);
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
