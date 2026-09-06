<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Attaches {@see Media} records to any model through the polymorphic
 * `mediables` pivot, bucketed by "zone" (cover, gallery, og, logo, avatar…).
 */
trait Mediable
{
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')
            ->withPivot(['zone', 'sort_order'])
            ->withTimestamps()
            ->orderBy('mediables.sort_order');
    }

    public function mediaInZone(string $zone): MorphToMany
    {
        return $this->media()->wherePivot('zone', $zone);
    }

    public function firstMedia(string $zone = 'cover'): ?Media
    {
        return $this->media->firstWhere('pivot.zone', $zone);
    }

    public function syncMedia(string $zone, array $mediaIds): void
    {
        $this->media()->wherePivot('zone', $zone)->detach();

        foreach (array_values(array_filter($mediaIds)) as $i => $id) {
            $this->media()->attach($id, ['zone' => $zone, 'sort_order' => $i]);
        }
    }

    public function attachMedia(int $mediaId, string $zone = 'cover', int $sort = 0): void
    {
        $this->media()->syncWithoutDetaching([
            $mediaId => ['zone' => $zone, 'sort_order' => $sort],
        ]);
    }
}
