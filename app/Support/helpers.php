<?php

use App\Models\Media;
use Hekmatinasser\Verta\Verta;

if (! function_exists('media')) {
    /**
     * Resolve a Media model by id with a per-request identity map, so the
     * same image referenced from several places (logo in header + footer +
     * OG tag, a hero image reused across sections) costs one query.
     */
    function media(int|string|null $id): ?Media
    {
        static $cache = [];

        if (blank($id)) {
            return null;
        }

        return $cache[$id] ??= Media::find($id);
    }
}

if (! function_exists('jdate')) {
    /**
     * Format a date in the Jalali calendar with Persian month names.
     * Falls back gracefully on null.
     */
    function jdate(mixed $date, string $format = 'j F Y'): string
    {
        if (blank($date)) {
            return '';
        }

        return Verta::instance($date)->format($format);
    }
}

if (! function_exists('jdate_iso')) {
    /** The underlying Gregorian ISO-8601 string, for <time datetime="..."> */
    function jdate_iso(mixed $date): string
    {
        return blank($date) ? '' : \Illuminate\Support\Carbon::parse($date)->toIso8601String();
    }
}
