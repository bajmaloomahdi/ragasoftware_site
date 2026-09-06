<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('public.products.index', [
            'seoOverrides' => ['title' => 'محصولات و راهکارها'],
            'products' => Product::published()->forCurrentLocale()
                ->with('heroImage', 'category')
                ->ordered()
                ->get(),
            'categories' => ProductCategory::forCurrentLocale()->orderBy('sort_order')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::published()->forCurrentLocale()
            ->where('slug', $slug)
            ->with(['heroImage', 'category', 'activeFeatures.image', 'seo', 'media'])
            ->firstOrFail();

        return view('public.products.show', [
            'product' => $product,
            'seoModel' => $product,
            'seoOverrides' => ['ogType' => 'product'],
            'related' => Product::published()->forCurrentLocale()
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
                ->ordered()->limit(3)->get(),
        ]);
    }
}
