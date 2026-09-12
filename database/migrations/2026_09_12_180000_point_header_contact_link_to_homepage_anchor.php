<?php

use App\Models\MenuItem;
use App\Models\NavigationMenu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The homepage now ends with a full contact section, so the header's
     * "تماس با ما" link should scroll there instead of navigating to the
     * standalone /contact page — matching how the other header items
     * (درباره ما, خدمات, محصولات, پروژه‌ها) already scroll on the homepage.
     * Only touches the item if it still has its original seeded value, so
     * an admin who already customised it is left alone.
     */
    public function up(): void
    {
        $header = NavigationMenu::where('location', 'header')->first();

        $header?->items()
            ->where('label', 'تماس با ما')
            ->where('link_value', '/contact')
            ->update(['link_value' => '/#contact']);
    }

    public function down(): void
    {
        $header = NavigationMenu::where('location', 'header')->first();

        $header?->items()
            ->where('label', 'تماس با ما')
            ->where('link_value', '/#contact')
            ->update(['link_value' => '/contact']);
    }
};
