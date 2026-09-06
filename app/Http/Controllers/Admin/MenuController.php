<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function index(): Response
    {
        $menus = NavigationMenu::with(['items' => fn ($q) => $q->orderBy('sort_order')])->get();

        return Inertia::render('Menus/Index', [
            'menus' => $menus,
            'linkTargets' => [
                'pages' => Page::where('status', 'published')->orderBy('title')->get(['id', 'title', 'slug']),
                'products' => Product::where('status', 'published')->orderBy('title')->get(['id', 'title', 'slug']),
                'services' => Service::where('status', 'published')->orderBy('title')->get(['id', 'title', 'slug']),
            ],
            'locations' => [
                'header' => 'منوی اصلی (هدر)',
                'footer_1' => 'فوتر - ستون ۱',
                'footer_2' => 'فوتر - ستون ۲',
                'footer_3' => 'فوتر - ستون ۳',
                'mobile' => 'منوی موبایل',
                'legal' => 'لینک‌های حقوقی',
            ],
        ]);
    }

    public function storeMenu(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:40', 'unique:navigation_menus,location'],
        ]);

        NavigationMenu::create($data);

        return back()->with('success', 'منو ساخته شد.');
    }

    /** Replace a menu's full item tree in one shot (from the drag-and-drop builder). */
    public function sync(Request $request, NavigationMenu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['present', 'array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.parent_key' => ['nullable'],
            'items.*.label' => ['required', 'string', 'max:120'],
            'items.*.link_type' => ['required', 'string', 'max:20'],
            'items.*.link_value' => ['nullable', 'string', 'max:255'],
            'items.*.target' => ['nullable', 'string', 'max:10'],
            'items.*.icon' => ['nullable', 'string', 'max:50'],
            'items.*.is_active' => ['boolean'],
        ]);

        DB::transaction(function () use ($menu, $validated) {
            $menu->items()->delete();

            $keyToId = [];
            foreach (array_values($validated['items']) as $i => $row) {
                $item = $menu->items()->create([
                    'parent_id' => isset($row['parent_key']) ? ($keyToId[$row['parent_key']] ?? null) : null,
                    'label' => $row['label'],
                    'link_type' => $row['link_type'],
                    'link_value' => $row['link_value'] ?? null,
                    'target' => $row['target'] ?? '_self',
                    'icon' => $row['icon'] ?? null,
                    'sort_order' => $i,
                    'is_active' => $row['is_active'] ?? true,
                ]);
                $keyToId[$row['id'] ?? "new-{$i}"] = $item->id;
            }
        });

        return back()->with('success', 'منو ذخیره شد.');
    }

    public function destroyMenu(NavigationMenu $menu): RedirectResponse
    {
        $menu->delete();

        return back()->with('success', 'منو حذف شد.');
    }
}
