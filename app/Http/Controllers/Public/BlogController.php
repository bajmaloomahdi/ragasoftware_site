<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->filled('category')
            ? BlogCategory::forCurrentLocale()->where('slug', $request->string('category'))->first()
            : null;

        $tag = $request->filled('tag')
            ? BlogTag::where('slug', $request->string('tag'))->first()
            : null;

        $posts = BlogPost::published()->forCurrentLocale()
            ->with(['cover', 'category', 'author'])
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->when($tag, fn ($q) => $q->whereHas('tags', fn ($q) => $q->where('blog_tags.id', $tag->id)))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
                ->where('title', 'like', '%'.$request->string('q').'%')
                ->orWhere('excerpt', 'like', '%'.$request->string('q').'%')))
            ->latestFirst()
            ->paginate(9)
            ->withQueryString();

        return view('public.blog.index', [
            'seoOverrides' => ['title' => $category?->name ? "وبلاگ — {$category->name}" : 'وبلاگ'],
            'posts' => $posts,
            'categories' => BlogCategory::forCurrentLocale()->withCount(['posts' => fn ($q) => $q->published()])->orderBy('sort_order')->get(),
            'activeCategory' => $category,
            'activeTag' => $tag,
            'featured' => BlogPost::published()->forCurrentLocale()->where('is_featured', true)->with('cover', 'category')->latestFirst()->first(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::published()->forCurrentLocale()
            ->where('slug', $slug)
            ->with(['cover', 'category', 'author', 'tags', 'seo'])
            ->firstOrFail();

        $post->incrementQuietly('views');

        return view('public.blog.show', [
            'post' => $post,
            'seoModel' => $post,
            'seoOverrides' => ['ogType' => 'article'],
            'related' => BlogPost::published()->forCurrentLocale()
                ->where('id', '!=', $post->id)
                ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
                ->latestFirst()->limit(3)->get(),
        ]);
    }
}
