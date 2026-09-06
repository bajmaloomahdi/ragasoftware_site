<?php

namespace App\Services\Settings;

use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Support\Collection;

/**
 * Cached read/write access to editable site-wide settings.
 *
 * Usage in Blade:  {{ $site->get('contact.phone') }}
 *                  {{ $site->get('general.company_name', 'راگا سافت‌ور') }}
 */
class SettingsRepository
{
    private const CACHE_KEY = 'site.settings';

    private const SOCIAL_KEY = 'site.social_links';

    /** @var array<string, mixed>|null */
    private ?array $cache = null;

    private ?Collection $socialCache = null;

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->cache ??= cache()->rememberForever(self::CACHE_KEY, function () {
            return SiteSetting::query()
                ->get()
                ->mapWithKeys(fn (SiteSetting $s) => [$s->key => $s->castValue()])
                ->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->all());
    }

    /** @return array<string, mixed> */
    public function group(string $group): array
    {
        $prefix = $group.'.';

        return collect($this->all())
            ->filter(fn ($v, $k) => str_starts_with($k, $prefix))
            ->all();
    }

    public function set(string $key, mixed $value, string $type = 'string', ?string $group = null): void
    {
        SiteSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value,
                'type' => $type,
                'group' => $group ?? explode('.', $key)[0],
            ],
        );

        $this->flush();
    }

    /** @return Collection<int, SocialLink> — memoised per request (models are never persisted to cache) */
    public function socialLinks(): Collection
    {
        return $this->socialCache ??= SocialLink::active()->get();
    }

    public function flush(): void
    {
        $this->cache = null;
        $this->socialCache = null;
        cache()->forget(self::CACHE_KEY);
        cache()->forget(self::SOCIAL_KEY);
    }
}
