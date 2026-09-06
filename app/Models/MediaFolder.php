<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaFolder extends Model
{
    use HasSlug;

    protected string $slugSource = 'name';

    protected $fillable = ['parent_id', 'name', 'slug', 'path', 'sort_order'];

    protected static function booted(): void
    {
        static::saving(function (MediaFolder $folder) {
            $parentPath = $folder->parent_id
                ? optional(MediaFolder::find($folder->parent_id))->path ?? '/'
                : '/';
            $folder->path = rtrim($parentPath, '/').'/'.$folder->slug;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'folder_id');
    }
}
