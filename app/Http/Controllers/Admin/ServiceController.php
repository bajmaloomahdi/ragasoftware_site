<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSeoMeta;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    use SavesSeoMeta;

    public function index(Request $request): Response
    {
        $rows = Service::query()
            ->with('image')
            ->when($request->string('search')->toString(), fn (Builder $q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
            ->orderBy(
                in_array($request->string('sort')->toString(), ['title', 'sort_order', 'published_at'], true) ? $request->string('sort')->toString() : 'sort_order',
                $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc',
            )
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Services/Index', [
            'rows' => $rows,
            'filters' => $request->only('search', 'status', 'sort', 'direction'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Services/Form', ['service' => null]);
    }

    public function edit(Service $service): Response
    {
        $service->load(['seo', 'image', 'features' => fn ($q) => $q->orderBy('sort_order')]);

        return Inertia::render('Services/Form', ['service' => $service]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        $service = DB::transaction(function () use ($request, $data) {
            $service = Service::create($this->attributes($data, $request));
            $this->syncFeatures($service, $data['features'] ?? []);
            $this->persistSeo($service, $request);

            return $service;
        });

        return redirect()->route('admin.services.edit', $service)->with('success', 'خدمت ایجاد شد.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $this->validated($request, $service);
        DB::transaction(function () use ($request, $service, $data) {
            $service->update($this->attributes($data, $request));
            $this->syncFeatures($service, $data['features'] ?? []);
            $this->persistSeo($service, $request);
        });

        return back()->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'خدمت حذف شد.');
    }

    private function validated(Request $request, ?Service $service): array
    {
        return $request->validate(array_merge([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('services', 'slug')->ignore($service?->id)],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:60'],
            'media_id' => ['nullable', 'exists:media,id'],
            'featured' => ['boolean'],
            'status' => ['required', Rule::in(config('cms.statuses'))],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['integer', 'min:0'],
            'features' => ['array'],
            'features.*.id' => ['nullable', 'integer'],
            'features.*.title' => ['required_with:features', 'string', 'max:150'],
            'features.*.description' => ['nullable', 'string', 'max:600'],
            'features.*.icon' => ['nullable', 'string', 'max:60'],
        ], $this->seoRules()));
    }

    private function attributes(array $data, Request $request): array
    {
        return [
            ...collect($data)->except(['features', 'seo'])->all(),
            'body' => empty($data['body']) ? null : clean($data['body']),
            'updated_by' => $request->user()->id,
            'created_by' => $request->user()->id,
        ];
    }

    private function syncFeatures(Service $service, array $features): void
    {
        $keepIds = [];

        foreach (array_values($features) as $i => $row) {
            $feature = $service->features()->updateOrCreate(
                ['id' => $row['id'] ?? null],
                [
                    'kind' => 'feature',
                    'title' => $row['title'],
                    'description' => $row['description'] ?? null,
                    'icon' => $row['icon'] ?? null,
                    'sort_order' => $i,
                    'is_active' => true,
                ],
            );
            $keepIds[] = $feature->id;
        }

        $service->features()->whereNotIn('id', $keepIds)->delete();
    }
}
