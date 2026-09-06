<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Auto-populates a URL slug from a source column, keeping it unique per
 * locale + model. Models may override:
 *   protected string $slugSource = 'title';
 *   protected string $slugColumn = 'slug';
 * A manually supplied slug is respected (only normalised), never overwritten.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model) {
            /** @var static $model */
            $source = $model->slugSourceColumn();
            $column = $model->slugColumn();

            $value = $model->{$column};

            if (blank($value) && filled($model->{$source})) {
                $value = $model->{$source};
            }

            if (blank($value)) {
                return;
            }

            $model->{$column} = $model->uniqueSlug(Str::slug($value, '-', null), $column);
        });
    }

    public function slugColumn(): string
    {
        return property_exists($this, 'slugColumn') ? $this->slugColumn : 'slug';
    }

    public function slugSourceColumn(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'title';
    }

    protected function uniqueSlug(string $slug, string $column): string
    {
        $slug = $slug !== '' ? $slug : Str::random(8);
        $original = $slug;
        $i = 2;

        while ($this->slugExists($slug, $column)) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, string $column): bool
    {
        $query = static::withoutGlobalScopes()
            ->where($column, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey());

        if (array_key_exists('locale', $this->getAttributes())) {
            $query->where('locale', $this->locale ?? config('cms.locales.default'));
        }

        if (method_exists($this, 'trashed')) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    public function getRouteKeyName(): string
    {
        return $this->slugColumn();
    }
}
