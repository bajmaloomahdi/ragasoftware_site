<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesSeoMeta;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    use SavesSeoMeta;

    public function index(Request $request): Response
    {
        $rows = BlogPost::query()
            ->with(['category:id,name', 'author:id,name', 'cover'])
            ->when($request->string('search')->toString(), fn (Builder $q, $s) => $q->where('title', 'like', "%{$s}%")->orWhere('excerpt', 'like', "%{$s}%"))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
            ->when($request->filled('category'), fn (Builder $q) => $q->where('category_id', $request->integer('category')))
            ->orderBy(
                in_array($request->string('sort')->toString(), ['title', 'published_at', 'created_at', 'views'], true) ? $request->string('sort')->toString() : 'created_at',
                $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc',
            )
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Blog/Index', [
            'rows' => $rows,
            'filters' => $request->only('search', 'status', 'category', 'sort', 'direction'),
            'categories' => BlogCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Blog/Form', $this->formData(null));
    }

    public function edit(BlogPost $post): Response
    {
        $post->load(['seo', 'cover', 'tags:id']);

        return Inertia::render('Blog/Form', $this->formData($post));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        $post = DB::transaction(function () use ($request, $data) {
            $post = BlogPost::create($this->attributes($data, $request));
            $post->tags()->sync($data['tag_ids'] ?? []);
            $this->persistSeo($post, $request);

            return $post;
        });

        return redirect()->route('admin.posts.edit', $post)->with('success', 'مقاله ایجاد شد.');
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $data = $this->validated($request, $post);
        DB::transaction(function () use ($request, $post, $data) {
            $post->update($this->attributes($data, $request));
            $post->tags()->sync($data['tag_ids'] ?? []);
            $this->persistSeo($post, $request);
        });

        return back()->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'مقاله حذف شد.');
    }

    private function formData(?BlogPost $post): array
    {
        return [
            'post' => $post,
            'categories' => BlogCategory::orderBy('name')->get(['id', 'name']),
            'tags' => BlogTag::orderBy('name')->get(['id', 'name']),
            'authors' => User::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validated(Request $request, ?BlogPost $post): array
    {
        return $request->validate(array_merge([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('blog_posts', 'slug')->ignore($post?->id)],
            'category_id' => ['nullable', 'exists:blog_categories,id'],
            'author_id' => ['nullable', 'exists:users,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'cover_media_id' => ['nullable', 'exists:media,id'],
            'is_featured' => ['boolean'],
            'status' => ['required', Rule::in(config('cms.statuses'))],
            'published_at' => ['nullable', 'date'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'exists:blog_tags,id'],
        ], $this->seoRules()));
    }

    private function attributes(array $data, Request $request): array
    {
        return [
            ...collect($data)->except(['tag_ids', 'seo'])->all(),
            'body' => empty($data['body']) ? null : clean($data['body']),
            'author_id' => $data['author_id'] ?? $request->user()->id,
            'updated_by' => $request->user()->id,
            'created_by' => $request->user()->id,
        ];
    }
}
