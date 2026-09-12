<?php

use App\Models\NavigationMenu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The homepage is a single-page experience with a section for each of
     * these — درباره ما/خدمات/محصولات/پروژه‌ها (تماس با ما was already fixed
     * in an earlier migration). The header menu was originally seeded
     * pointing at the old standalone pages (/about, /services, /products,
     * /projects) before that redesign, and since a page's own header menu
     * row is never re-seeded on deploy, production is still on those old
     * links. Only rewrites an item that isn't already an anchor link (an
     * admin who deliberately set something else, including a plain "/",
     * is left alone).
     */
    private const LABEL_TO_ANCHOR = [
        'درباره ما' => '/#about',
        'خدمات' => '/#services',
        'محصولات' => '/#products',
        'پروژه‌ها' => '/#projects',
    ];

    public function up(): void
    {
        $header = NavigationMenu::where('location', 'header')->first();
        if (! $header) {
            return;
        }

        foreach (self::LABEL_TO_ANCHOR as $label => $anchorUrl) {
            $header->items()
                ->where('label', $label)
                ->where('link_type', 'url')
                ->where('link_value', 'not like', '/#%')
                ->update(['link_value' => $anchorUrl]);
        }
    }

    public function down(): void
    {
        $header = NavigationMenu::where('location', 'header')->first();
        if (! $header) {
            return;
        }

        $original = [
            'درباره ما' => '/about',
            'خدمات' => '/services',
            'محصولات' => '/products',
            'پروژه‌ها' => '/projects',
        ];

        foreach ($original as $label => $url) {
            $header->items()
                ->where('label', $label)
                ->where('link_value', self::LABEL_TO_ANCHOR[$label])
                ->update(['link_value' => $url]);
        }
    }
};
