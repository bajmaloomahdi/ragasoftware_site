<?php

namespace App\Services\Cms;

/**
 * The catalogue of page-section *types*. Structure lives in config/cms.php;
 * this class exposes it to the admin editor and sanitises the JSON payload
 * saved back into `page_sections.settings`.
 */
class SectionRegistry
{
    /** @return array<string, mixed> */
    public static function all(): array
    {
        return config('cms.sections', []);
    }

    public static function get(string $type): ?array
    {
        return config("cms.sections.{$type}");
    }

    public static function exists(string $type): bool
    {
        return self::get($type) !== null;
    }

    /**
     * Shape the registry for the React section builder.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function forEditor(): array
    {
        $out = [];

        foreach (self::all() as $type => $def) {
            $out[] = [
                'type' => $type,
                'label' => $def['label'] ?? $type,
                'icon' => $def['icon'] ?? 'BlockOutlined',
                'fields' => self::normaliseFields($def['fields'] ?? []),
            ];
        }

        return $out;
    }

    /** @return array<int, array<string, mixed>> */
    private static function normaliseFields(array $fields): array
    {
        $out = [];

        foreach ($fields as $key => $spec) {
            $entry = ['key' => $key] + $spec;

            if (($spec['type'] ?? null) === 'repeater' && isset($spec['item'])) {
                $entry['item'] = self::normaliseFields($spec['item']);
            }

            $out[] = $entry;
        }

        return $out;
    }

    /**
     * Keep only keys that the type declares; run rich-text fields through the
     * HTML purifier. Nested repeater items are handled recursively.
     */
    public static function sanitizeSettings(string $type, array $settings): array
    {
        $def = self::get($type);
        if (! $def) {
            return [];
        }

        return self::sanitizeAgainst($def['fields'] ?? [], $settings);
    }

    private static function sanitizeAgainst(array $fields, array $input): array
    {
        $clean = [];

        foreach ($fields as $key => $spec) {
            if (! array_key_exists($key, $input)) {
                continue;
            }

            $value = $input[$key];
            $fieldType = $spec['type'] ?? 'text';

            $clean[$key] = match ($fieldType) {
                'richtext' => is_string($value) ? clean($value) : null,
                'boolean' => (bool) $value,
                'number' => is_numeric($value) ? $value + 0 : null,
                'repeater' => collect(is_array($value) ? $value : [])
                    ->map(fn ($row) => is_array($row) ? self::sanitizeAgainst($spec['item'] ?? [], $row) : [])
                    ->all(),
                'relation', 'media_multiple' => array_values(array_filter((array) $value)),
                default => is_scalar($value) ? $value : null,
            };
        }

        return $clean;
    }
}
