<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('public.projects.index', [
            'seoOverrides' => ['title' => 'نمونه‌کارها'],
            'projects' => Project::published()->forCurrentLocale()->with('cover', 'customer')->ordered()->paginate(12),
        ]);
    }

    public function show(string $slug): View
    {
        $project = Project::published()->forCurrentLocale()
            ->where('slug', $slug)
            ->with(['cover', 'customer', 'seo', 'media'])
            ->firstOrFail();

        return view('public.projects.show', [
            'project' => $project,
            'seoModel' => $project,
            'gallery' => $project->mediaInZone('gallery')->get(),
            'related' => Project::published()->forCurrentLocale()
                ->where('id', '!=', $project->id)->ordered()->limit(3)->get(),
        ]);
    }
}
