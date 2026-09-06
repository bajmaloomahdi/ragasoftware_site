<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('public.services.index', [
            'seoOverrides' => ['title' => 'خدمات'],
            'services' => Service::published()->forCurrentLocale()->with('image')->ordered()->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $service = Service::published()->forCurrentLocale()
            ->where('slug', $slug)
            ->with(['image', 'features', 'seo'])
            ->firstOrFail();

        return view('public.services.show', [
            'service' => $service,
            'seoModel' => $service,
            'related' => Service::published()->forCurrentLocale()
                ->where('id', '!=', $service->id)->ordered()->limit(3)->get(),
        ]);
    }
}
