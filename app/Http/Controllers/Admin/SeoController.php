<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect as RedirectModel;
use App\Services\Seo\SitemapBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SeoController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Seo/Edit', [
            'redirects' => RedirectModel::orderByDesc('id')->paginate(20)->withQueryString(),
            'sitemapUrl' => url('/sitemap.xml'),
            'robotsUrl' => url('/robots.txt'),
        ]);
    }

    public function storeRedirect(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_path' => ['required', 'string', 'max:255', 'unique:redirects,from_path'],
            'to_path' => ['required', 'string', 'max:255'],
            'status_code' => ['required', Rule::in([301, 302])],
            'is_active' => ['boolean'],
        ]);

        RedirectModel::create($data);

        return back()->with('success', 'ریدایرکت ثبت شد.');
    }

    public function updateRedirect(Request $request, RedirectModel $redirect): RedirectResponse
    {
        $redirect->update($request->validate([
            'from_path' => ['required', 'string', 'max:255', Rule::unique('redirects', 'from_path')->ignore($redirect->id)],
            'to_path' => ['required', 'string', 'max:255'],
            'status_code' => ['required', Rule::in([301, 302])],
            'is_active' => ['boolean'],
        ]));

        return back()->with('success', 'ریدایرکت به‌روزرسانی شد.');
    }

    public function destroyRedirect(RedirectModel $redirect): RedirectResponse
    {
        $redirect->delete();

        return back()->with('success', 'ریدایرکت حذف شد.');
    }

    public function rebuildSitemap(SitemapBuilder $builder): RedirectResponse
    {
        $builder->forget();
        $builder->build();

        return back()->with('success', 'نقشه سایت بازسازی شد.');
    }
}
