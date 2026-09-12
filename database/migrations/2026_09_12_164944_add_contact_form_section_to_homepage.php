<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Appends a "فرم تماس" section to the end of the homepage's section
     * list, if it doesn't already have one — purely additive, never touches
     * any existing section (content an editor has since changed is safe).
     */
    public function up(): void
    {
        $home = Page::where('is_homepage', true)->first();

        if (! $home || $home->sections()->where('type', 'contact_form')->exists()) {
            return;
        }

        $maxSortOrder = (int) $home->sections()->max('sort_order');

        $home->sections()->create([
            'type' => 'contact_form',
            'name' => 'فرم تماس',
            'sort_order' => $maxSortOrder + 1,
            'is_active' => true,
            'settings' => [
                'heading' => 'با ما در تماس باشید',
                'subheading' => 'برای مشاوره، دمو یا شروع همکاری، فرم زیر را پر کنید یا مستقیم با ما تماس بگیرید.',
                'show_contact_info' => true,
            ],
        ]);
    }

    public function down(): void
    {
        $home = Page::where('is_homepage', true)->first();
        $home?->sections()->where('type', 'contact_form')->where('name', 'فرم تماس')->delete();
    }
};
