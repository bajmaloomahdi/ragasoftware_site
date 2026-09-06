<?php

namespace App\Models;

use App\Models\Concerns\HasLocale;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory, HasSlug, HasLocale;

    protected string $slugSource = 'name';

    protected $fillable = [
        'locale', 'translation_group', 'name', 'slug', 'role_title', 'bio',
        'photo_media_id', 'email', 'linkedin_url', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_media_id');
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true)->orderBy('sort_order');
    }
}
