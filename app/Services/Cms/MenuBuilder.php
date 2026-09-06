<?php

namespace App\Services\Cms;

use App\Models\MenuItem;
use App\Models\NavigationMenu;
use Illuminate\Support\Collection;

/**
 * Builds a cached, ready-to-render tree for a menu location.
 *
 * Blade:  @foreach($siteMenus->location('header') as $item) ... @endforeach
 * Each node: { label, url, target, icon, active, children[] }
 */
class MenuBuilder
{
    private const CACHE_KEY = 'site.menus';

    /** @var array<string, array>|null */
    private ?array $cache = null;

    /** @return array<int, array<string, mixed>> */
    public function location(string $location): array
    {
        return $this->tree()[$location]['items'] ?? [];
    }

    public function label(string $location): ?string
    {
        return $this->tree()[$location]['name'] ?? null;
    }

    public function has(string $location): bool
    {
        return ! empty($this->tree()[$location]['items']);
    }

    /** @return array<string, array> */
    public function tree(): array
    {
        return $this->cache ??= cache()->rememberForever(self::CACHE_KEY, function () {
            $menus = NavigationMenu::with(['items' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])->get();

            return $menus->mapWithKeys(fn (NavigationMenu $menu) => [
                $menu->location => [
                    'name' => $menu->name,
                    'items' => $this->buildNodes($menu->items, null),
                ],
            ])->all();
        });
    }

    /** @param Collection<int, MenuItem> $items */
    private function buildNodes(Collection $items, ?int $parentId): array
    {
        return $items
            ->where('parent_id', $parentId)
            ->map(fn (MenuItem $item) => [
                'label' => $item->label,
                'url' => $item->resolveUrl(),
                'target' => $item->target,
                'icon' => $item->icon,
                'children' => $this->buildNodes($items, $item->id),
            ])
            ->values()
            ->all();
    }

    public function flush(): void
    {
        $this->cache = null;
        cache()->forget(self::CACHE_KEY);
    }
}
