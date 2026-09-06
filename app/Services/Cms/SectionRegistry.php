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

    /**
     * Hydrate a stored section's settings into ready-to-render view data:
     * resolve media ids to Media models and, for data-backed sections
     * (products_grid, blog_latest, …), pull the actual published records.
     *
     * @return array<string, mixed>
     */
    public static function resolve(\App\Models\PageSection $section): array
    {
        $def = self::get($section->type) ?? [];
        $settings = $section->settings ?? [];
        $data = ['section' => $section, 'settings' => $settings, 'type' => $section->type];

        // resolve single + multiple media references anywhere in settings
        $data['media'] = isset($settings['media_id']) ? media($settings['media_id']) : null;

        if (! empty($settings['media_ids']) && is_array($settings['media_ids'])) {
            $data['medias'] = \App\Models\Media::whereIn('id', $settings['media_ids'])->get()
                ->sortBy(fn ($m) => array_search($m->id, $settings['media_ids']))->values();
        }

        $limit = (int) ($settings['limit'] ?? 6) ?: 6;

        $data['items'] = match ($def['source'] ?? null) {
            'products' => self::resolveProducts($settings, $limit),
            'services' => self::resolveServices($settings, $limit),
            'projects' => \App\Models\Project::published()->forCurrentLocale()->with('cover')->ordered()->limit($limit)->get(),
            'customers' => \App\Models\Customer::forCurrentLocale()->active()->with('logo')->get(),
            'testimonials' => \App\Models\Testimonial::forCurrentLocale()->active()->with('customer')->limit($limit)->get(),
            'team' => \App\Models\TeamMember::forCurrentLocale()->active()->with('photo')->get(),
            'blog' => self::resolvePosts($settings, $limit),
            'faqs' => self::resolveFaqs($settings, $limit),
            default => collect(),
        };

        return $data;
    }

    private static function resolveProducts(array $s, int $limit)
    {
        $q = \App\Models\Product::published()->forCurrentLocale()->with('heroImage')->ordered();

        return match ($s['mode'] ?? 'featured') {
            'all' => $q->limit($limit)->get(),
            'custom' => ! empty($s['item_ids'])
                ? \App\Models\Product::published()->forCurrentLocale()->with('heroImage')
                    ->where(fn ($q) => $q->whereIn('slug', $s['item_ids'])->orWhereIn('id', $s['item_ids']))->get()
                : collect(),
            default => $q->featured()->limit($limit)->get(),
        };
    }

    private static function resolveServices(array $s, int $limit)
    {
        $q = \App\Models\Service::published()->forCurrentLocale()->with('image')->ordered();

        return match ($s['mode'] ?? 'featured') {
            'all' => $q->limit($limit)->get(),
            'custom' => ! empty($s['item_ids'])
                ? \App\Models\Service::published()->forCurrentLocale()
                    ->where(fn ($q) => $q->whereIn('slug', $s['item_ids'])->orWhereIn('id', $s['item_ids']))->get()
                : collect(),
            default => $q->featured()->limit($limit)->get(),
        };
    }

    private static function resolvePosts(array $s, int $limit)
    {
        return \App\Models\BlogPost::published()->forCurrentLocale()
            ->with(['cover', 'category', 'author'])
            ->when(! empty($s['category_id']), fn ($q) => $q->where('category_id', $s['category_id']))
            ->latestFirst()
            ->limit($limit)
            ->get();
    }

    private static function resolveFaqs(array $s, int $limit)
    {
        return \App\Models\Faq::forCurrentLocale()->active()
            ->when(! empty($s['category_id']), fn ($q) => $q->where('category_id', $s['category_id']))
            ->limit($limit)
            ->get();
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
