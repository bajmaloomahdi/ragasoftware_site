<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $categories = FaqCategory::forCurrentLocale()
            ->with(['faqs' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($c) => $c->faqs->isNotEmpty());

        $uncategorised = Faq::forCurrentLocale()->active()->whereNull('category_id')->get();

        return view('public.faq', [
            'seoOverrides' => ['title' => 'سؤالات متداول'],
            ...compact('categories', 'uncategorised'),
        ]);
    }
}
