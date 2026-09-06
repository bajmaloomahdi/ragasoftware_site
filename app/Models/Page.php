<?php

namespace App\Models;

use App\Models\Concerns\HasLocale;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSeoMeta;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\Mediable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasLocale, HasPublishing, HasSeoMeta, Mediable;

    protected $fillable = [
        'locale', 'translation_group', 'title', 'slug', 'template', 'excerpt',
        'status', 'published_at', 'is_homepage', 'show_in_sitemap', 'sort_order',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_homepage' => 'boolean',
        'show_in_sitemap' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function activeSections(): HasMany
    {
        return $this->sections()->where('is_active', true);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function path(): string
    {
        return $this->is_homepage ? '/' : '/'.$this->slug;
    }

    public function publicPath(?string $slug = null): ?string
    {
        return $this->is_homepage ? null : '/'.($slug ?? $this->slug);
    }

    public function seoFallbacks(): array
    {
        return [
            'meta_title' => $this->title,
            'meta_description' => $this->excerpt,
        ];
    }
}
