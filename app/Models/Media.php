<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'folder_id', 'disk', 'path', 'filename', 'original_name', 'mime_type',
        'extension', 'size', 'width', 'height', 'alt_text', 'title', 'caption',
        'variants', 'uploaded_by',
    ];

    protected $casts = [
        'variants' => 'array',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected $appends = ['url', 'thumb_url'];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function isSvg(): bool
    {
        return $this->mime_type === 'image/svg+xml';
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /** Best URL for a given target width, falling back to the original. */
    public function variantUrl(string $size = 'md', bool $webp = false): string
    {
        $variants = $this->variants ?? [];

        if ($webp && isset($variants['webp'][$size])) {
            return Storage::disk($this->disk)->url($variants['webp'][$size]);
        }

        if (isset($variants[$size])) {
            return Storage::disk($this->disk)->url($variants[$size]);
        }

        return $this->url;
    }

    public function getThumbUrlAttribute(): string
    {
        return $this->isImage() && ! $this->isSvg() ? $this->variantUrl('thumb') : $this->url;
    }

    /** A ready-to-use srcset string across all generated raster widths. */
    public function srcset(bool $webp = false): string
    {
        $widths = config('cms.media.variants');
        $pairs = [];

        foreach ($widths as $name => $w) {
            $variants = $this->variants ?? [];
            $key = $webp ? ($variants['webp'][$name] ?? null) : ($variants[$name] ?? null);
            if ($key) {
                $pairs[] = Storage::disk($this->disk)->url($key)." {$w}w";
            }
        }

        return implode(', ', $pairs);
    }
}
