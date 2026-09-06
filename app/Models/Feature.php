<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Feature extends Model
{
    protected $fillable = [
        'featureable_type', 'featureable_id', 'kind', 'title',
        'description', 'icon', 'media_id', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function featureable(): MorphTo
    {
        return $this->morphTo();
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
