<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'meta_title', 'meta_description', 'focus_keyword', 'canonical_url',
        'meta_robots', 'no_index', 'og_title', 'og_description', 'og_media_id',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_media_id',
        'schema_type', 'schema_overrides',
    ];

    protected $casts = [
        'no_index' => 'boolean',
        'schema_overrides' => 'array',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    public function twitterMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'twitter_media_id');
    }
}
