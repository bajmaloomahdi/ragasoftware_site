<?php

namespace App\Models\Concerns;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Gives a model an editable, polymorphic {@see SeoMeta} row. Fallback values
 * (when a field is empty) are resolved by {@see \App\Services\Seo\SeoResolver}
 * from the model itself and the editable site settings.
 */
trait HasSeoMeta
{
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function seoOrNew(): SeoMeta
    {
        return $this->seo ?: $this->seo()->make();
    }

    /** Sensible per-model fallbacks; models may override. */
    public function seoFallbacks(): array
    {
        return [
            'meta_title' => $this->title ?? $this->name ?? null,
            'meta_description' => $this->summary ?? $this->excerpt ?? null,
        ];
    }
}
