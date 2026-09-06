<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSeoMeta;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    use SavesSeoMeta;

    public function index(Request $request): Response
    {
        $rows = Product::query()
            ->with(['category:id,name', 'heroImage'])
            ->when($request->string('search')->toString(), fn (Builder $q, $s) => $q->where('title', 'like', "%{$s}%")->orWhere('tagline', 'like', "%{$s}%"))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
            ->when($request->filled('category'), fn (Builder $q) => $q->where('category_id', $request->integer('category')))
            ->orderBy(
                in_array($request->string('sort')->toString(), ['title', 'sort_order', 'published_at', 'created_at'], true) ? $request->string('sort')->toString() : 'sort_order',
                $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc',
            )
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'rows' => $rows,
            'filters' => $request->only('search', 'status', 'category', 'sort', 'direction'),
            'categories' => ProductCategory::orderBy('sort_order')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Products/Form', [
            'product' => null,
            'categories' => ProductCategory::orderBy('sort_order')->get(['id', 'name']),
        ]);
    }

    public function edit(Product $product): Response
    {
        $product->load(['features' => fn ($q) => $q->orderBy('sort_order'), 'seo', 'heroImage', 'media']);

        return Inertia::render('Products/Form', [
            'product' => $product,
            'categories' => ProductCategory::orderBy('sort_order')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        $product = DB::transaction(function () use ($request, $data) {
            $product = Product::create($this->attributes($data, $request));
            $this->syncFeatures($product, $data['features'] ?? []);
            $this->persistSeo($product, $request);

            return $product;
        });

        return redirect()->route('admin.products.edit', $product)->with('success', 'محصول ایجاد شد.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        DB::transaction(function () use ($request, $product, $data) {
            $product->update($this->attributes($data, $request));
            $this->syncFeatures($product, $data['features'] ?? []);
            $this->persistSeo($product, $request);
        });

        return back()->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'محصول حذف شد.');
    }

    private function validated(Request $request, ?Product $product): array
    {
        return $request->validate(array_merge([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($product?->id)],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:60'],
            'hero_media_id' => ['nullable', 'exists:media,id'],
            'featured' => ['boolean'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(config('cms.statuses'))],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['integer', 'min:0'],
            'features' => ['array'],
            'features.*.id' => ['nullable', 'integer'],
            'features.*.title' => ['required_with:features', 'string', 'max:150'],
            'features.*.description' => ['nullable', 'string', 'max:600'],
            'features.*.icon' => ['nullable', 'string', 'max:60'],
            'features.*.media_id' => ['nullable', 'exists:media,id'],
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

    private function syncFeatures(Product $product, array $features): void
    {
        $keepIds = [];

        foreach (array_values($features) as $i => $row) {
            $feature = $product->features()->updateOrCreate(
                ['id' => $row['id'] ?? null],
                [
                    'title' => $row['title'],
                    'description' => $row['description'] ?? null,
                    'icon' => $row['icon'] ?? null,
                    'media_id' => $row['media_id'] ?? null,
                    'sort_order' => $i,
                    'is_active' => true,
                ],
            );
            $keepIds[] = $feature->id;
        }

        $product->features()->whereNotIn('id', $keepIds)->delete();
    }
}
