@extends('layouts.public')
@php
    $title = $activeCategory?->name ? "وبلاگ — {$activeCategory->name}" : 'وبلاگ';
    $description = $site->get('seo.default_description');
@endphp

@section('content')
    <x-breadcrumbs :items="array_values(array_filter([
        ['label' => 'وبلاگ', 'url' => route('blog.index')],
        $activeCategory ? ['label' => $activeCategory->name] : null,
        $activeTag ? ['label' => '#'.$activeTag->name] : null,
    ]))" />

    <div class="container-site section-pad">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-extrabold text-navy-900 sm:text-4xl">
                {{ $activeCategory?->name ?? ($activeTag ? '#'.$activeTag->name : 'وبلاگ راگا سافت‌ور') }}
            </h1>
            <p class="mt-4 lead">تحلیل‌ها و تجربه‌های ما درباره نرم‌افزار سازمانی، تحول دیجیتال و مدیریت داده.</p>
        </div>

        @if($featured && !$activeCategory && !$activeTag && $posts->currentPage() === 1)
            <a href="{{ route('blog.show', $featured->slug) }}" class="mt-10 grid gap-6 overflow-hidden rounded-3xl border border-mist-200 bg-white lg:grid-cols-2">
                <x-picture :media="$featured->cover" ratio="16/10" class="h-full w-full object-cover" />
                <div class="flex flex-col justify-center p-8">
                    @if($featured->category)<span class="eyebrow">{{ $featured->category->name }}</span>@endif
                    <h2 class="text-2xl font-bold text-navy-900">{{ $featured->title }}</h2>
                    <p class="mt-3 text-navy-600">{{ \Illuminate\Support\Str::limit($featured->excerpt, 160) }}</p>
                </div>
            </a>
        @endif

        <div class="mt-10 grid gap-10 lg:grid-cols-4">
            <div class="lg:col-span-3">
                @if($posts->isEmpty())
                    <p class="text-navy-500">مقاله‌ای یافت نشد.</p>
                @else
                    <div class="grid gap-6 sm:grid-cols-2">
                        @foreach($posts as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $posts->links() }}</div>
                @endif
            </div>

            <aside class="lg:col-span-1">
                <h2 class="mb-4 text-sm font-bold text-navy-900">دسته‌بندی‌ها</h2>
                <ul class="space-y-1 text-sm">
                    <li><a href="{{ route('blog.index') }}" @class(['block rounded-lg px-3 py-2 hover:bg-mist-100', 'bg-mist-100 font-semibold text-brand-600' => !$activeCategory])>همه</a></li>
                    @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('blog.index', ['category' => $cat->slug]) }}"
                               @class(['flex items-center justify-between rounded-lg px-3 py-2 hover:bg-mist-100', 'bg-mist-100 font-semibold text-brand-600' => $activeCategory?->id === $cat->id])>
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs text-navy-400">{{ $cat->posts_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>
        </div>
    </div>
@endsection
