<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Every content row carries a `locale` (default "fa") and a `translation_group`
 * uuid that ties translations of the same logical item together. Adding a
 * second language later = inserting rows, no migration.
 */
trait HasLocale
{
    public static function bootHasLocale(): void
    {
        static::creating(function (Model $model) {
            if (blank($model->locale)) {
                $model->locale = app()->getLocale() ?: config('cms.locales.default', 'fa');
            }
            if (blank($model->translation_group)) {
                $model->translation_group = (string) Str::uuid();
            }
        });
    }

    public function scopeForLocale(Builder $query, ?string $locale = null): void
    {
        $query->where('locale', $locale ?: app()->getLocale());
    }

    public function scopeForCurrentLocale(Builder $query): void
    {
        $query->where('locale', app()->getLocale());
    }

    /** Sibling rows in other languages. */
    public function translations()
    {
        return static::query()
            ->where('translation_group', $this->translation_group)
            ->where($this->getKeyName(), '!=', $this->getKey());
    }
}
