<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['from_path', 'to_path', 'status_code', 'hits', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'status_code' => 'integer',
        'hits' => 'integer',
    ];

    protected static function booted(): void
    {
        $flush = fn () => cache()->forget('site.redirects');
        static::saved($flush);
        static::deleted($flush);
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true);
    }
}
