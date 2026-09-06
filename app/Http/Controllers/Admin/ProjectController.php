<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSeoMeta;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    use SavesSeoMeta;

    public function index(Request $request): Response
    {
        $rows = Project::query()
            ->with(['cover', 'customer:id,name'])
            ->when($request->string('search')->toString(), fn (Builder $q, $s) => $q->where('title', 'like', "%{$s}%")->orWhere('client_name', 'like', "%{$s}%"))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
            ->orderBy(
                in_array($request->string('sort')->toString(), ['title', 'sort_order', 'completed_on', 'published_at'], true) ? $request->string('sort')->toString() : 'sort_order',
                $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc',
            )
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Projects/Index', [
            'rows' => $rows,
            'filters' => $request->only('search', 'status', 'sort', 'direction'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Form', [
            'project' => null,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(Project $project): Response
    {
        $project->load(['seo', 'cover', 'media']);

        return Inertia::render('Projects/Form', [
            'project' => $project,
            'gallery' => $project->mediaInZone('gallery')->get(),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        $project = DB::transaction(function () use ($request, $data) {
            $project = Project::create($this->attributes($data, $request));
            $project->syncMedia('gallery', $data['gallery_ids'] ?? []);
            $this->persistSeo($project, $request);

            return $project;
        });

        return redirect()->route('admin.projects.edit', $project)->with('success', 'نمونه‌کار ایجاد شد.');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validated($request, $project);
        DB::transaction(function () use ($request, $project, $data) {
            $project->update($this->attributes($data, $request));
            $project->syncMedia('gallery', $data['gallery_ids'] ?? []);
            $this->persistSeo($project, $request);
        });

        return back()->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'نمونه‌کار حذف شد.');
    }

    private function validated(Request $request, ?Project $project): array
    {
        return $request->validate(array_merge([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('projects', 'slug')->ignore($project?->id)],
            'client_name' => ['nullable', 'string', 'max:150'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'cover_media_id' => ['nullable', 'exists:media,id'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'completed_on' => ['nullable', 'date'],
            'featured' => ['boolean'],
            'status' => ['required', Rule::in(config('cms.statuses'))],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['integer', 'min:0'],
            'gallery_ids' => ['array'],
            'gallery_ids.*' => ['integer', 'exists:media,id'],
        ], $this->seoRules()));
    }

    private function attributes(array $data, Request $request): array
    {
        return [
            ...collect($data)->except(['gallery_ids', 'seo'])->all(),
            'body' => empty($data['body']) ? null : clean($data['body']),
            'updated_by' => $request->user()->id,
            'created_by' => $request->user()->id,
        ];
    }
}
