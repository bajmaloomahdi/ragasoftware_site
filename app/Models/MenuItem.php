<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'link_type', 'link_value',
        'target', 'icon', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        $flush = fn () => cache()->forget('site.menus');
        static::saved($flush);
        static::deleted($flush);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /** Resolves this item to a public URL based on its link_type. */
    public function resolveUrl(): string
    {
        return match ($this->link_type) {
            'route' => rescue(fn () => route($this->link_value), '#', false),
            'page' => url('/'.ltrim((string) $this->link_value, '/')),
            'product' => url('/products/'.$this->link_value),
            'service' => url('/services/'.$this->link_value),
            'project' => url('/projects/'.$this->link_value),
            'blog_index' => url('/blog'),
            'blog_category' => url('/blog/category/'.$this->link_value),
            default => (string) ($this->link_value ?: '#'),
        };
    }
}
