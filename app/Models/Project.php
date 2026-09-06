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
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasLocale, HasPublishing, HasSeoMeta, Mediable;

    protected $fillable = [
        'locale', 'translation_group', 'title', 'slug', 'client_name', 'customer_id',
        'summary', 'body', 'cover_media_id', 'project_url', 'completed_on', 'featured',
        'status', 'published_at', 'sort_order', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'completed_on' => 'date',
        'featured' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function publicPath(?string $slug = null): string
    {
        return '/projects/'.($slug ?? $this->slug);
    }

    public function scopeOrdered(Builder $q): void
    {
        $q->orderBy('sort_order')->orderByDesc('published_at');
    }
}
