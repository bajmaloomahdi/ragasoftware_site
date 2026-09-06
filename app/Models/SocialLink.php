<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = ['platform', 'label', 'url', 'icon', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        $flush = fn () => cache()->forget('site.social_links');
        static::saved($flush);
        static::deleted($flush);
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true)->orderBy('sort_order');
    }
}
