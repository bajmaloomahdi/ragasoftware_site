<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\Cms\SectionRenderer;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(SectionRenderer $renderer): View
    {
        $page = Page::query()
            ->where('is_homepage', true)
            ->with('seo')
            ->firstOrFail();

        return view('public.page', [
            'page' => $page,
            'sectionsHtml' => $renderer->render($page),
            'isHome' => true,
        ]);
    }

    public function show(string $slug, SectionRenderer $renderer): View
    {
        $page = Page::query()
            ->published()
            ->forCurrentLocale()
            ->where('slug', $slug)
            ->where('is_homepage', false)
            ->with('seo')
            ->firstOrFail();

        return view('public.page', [
            'page' => $page,
            'sectionsHtml' => $renderer->render($page),
            'isHome' => false,
        ]);
    }
}
