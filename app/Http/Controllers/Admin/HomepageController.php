<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;

class HomepageController extends Controller
{
    /** The homepage is just the Page flagged is_homepage; edit it in the page builder. */
    public function edit(): RedirectResponse
    {
        $page = Page::firstOrCreate(
            ['is_homepage' => true],
            [
                'title' => 'صفحه اصلی',
                'slug' => 'home',
                'template' => 'landing',
                'status' => 'published',
                'published_at' => now(),
                'show_in_sitemap' => true,
            ],
        );

        return redirect()->route('admin.pages.edit', $page);
    }
}
