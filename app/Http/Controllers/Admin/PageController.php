<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSeoMeta;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\Cms\SectionRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    use SavesSeoMeta;

    public function index(Request $request): Response
    {
        $rows = Page::query()
            ->when($request->string('search')->toString(), fn (Builder $q, $s) => $q->where('title', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%"))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
            ->orderByDesc('is_homepage')
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Pages/Index', [
            'rows' => $rows,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Pages/Form', [
            'page' => null,
            'sectionTypes' => SectionRegistry::forEditor(),
        ]);
    }

    public function edit(Page $page): Response
    {
        $page->load(['sections' => fn ($q) => $q->orderBy('sort_order'), 'seo']);

        return Inertia::render('Pages/Form', [
            'page' => $page,
            'sectionTypes' => SectionRegistry::forEditor(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);

        $page = DB::transaction(function () use ($request, $data) {
            $page = Page::create($this->attributes($data, $request, creating: true));
            $this->syncSections($page, $data['sections'] ?? []);
            $this->persistSeo($page, $request);

            return $page;
        });

        return redirect()->route('admin.pages.edit', $page)->with('success', 'صفحه ایجاد شد.');
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request, $page);

        DB::transaction(function () use ($request, $page, $data) {
            $page->update($this->attributes($data, $request, creating: false));
            $this->syncSections($page, $data['sections'] ?? []);
            $this->persistSeo($page, $request);
        });

        return back()->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        abort_if($page->is_homepage, 403, 'صفحه اصلی قابل حذف نیست.');

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'صفحه حذف شد.');
    }

    private function validated(Request $request, ?Page $page): array
    {
        return $request->validate(array_merge([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('pages', 'slug')->ignore($page?->id)],
            'template' => ['required', Rule::in(['default', 'full_width', 'landing'])],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(config('cms.statuses'))],
            'published_at' => ['nullable', 'date'],
            'show_in_sitemap' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'sections' => ['array'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.type' => ['required_with:sections', 'string', Rule::in(array_keys(config('cms.sections')))],
            'sections.*.name' => ['nullable', 'string', 'max:120'],
            'sections.*.settings' => ['nullable', 'array'],
            'sections.*.is_active' => ['boolean'],
        ], $this->seoRules()));
    }

    private function attributes(array $data, Request $request, bool $creating): array
    {
        $attrs = collect($data)->except(['sections', 'seo'])->all();
        $attrs['updated_by'] = $request->user()->id;
        if ($creating) {
            $attrs['created_by'] = $request->user()->id;
        }

        return $attrs;
    }

    private function syncSections(Page $page, array $sections): void
    {
        $keepIds = [];

        foreach (array_values($sections) as $i => $row) {
            $section = $page->sections()->updateOrCreate(
                ['id' => $row['id'] ?? null],
                [
                    'type' => $row['type'],
                    'name' => $row['name'] ?? null,
                    'settings' => SectionRegistry::sanitizeSettings($row['type'], $row['settings'] ?? []),
                    'sort_order' => $i,
                    'is_active' => $row['is_active'] ?? true,
                ],
            );
            $keepIds[] = $section->id;
        }

        $page->sections()->whereNotIn('id', $keepIds)->delete();
    }
}
