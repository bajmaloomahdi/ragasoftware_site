@extends('layouts.public')
@php
    $title = $post->seo?->meta_title ?: $post->title;
    $description = $post->seo?->meta_description ?: $post->excerpt;
    $ogType = 'article';
@endphp

@section('content')
    <x-breadcrumbs :items="array_values(array_filter([
        ['label' => 'وبلاگ', 'url' => route('blog.index')],
        $post->category ? ['label' => $post->category->name, 'url' => route('blog.index', ['category' => $post->category->slug])] : null,
        ['label' => $post->title],
    ]))" />

    <article class="section-pad">
        <div class="container-site max-w-3xl">
            <header>
                @if($post->category)<span class="eyebrow">{{ $post->category->name }}</span>@endif
                <h1 class="text-3xl font-extrabold leading-tight text-ink-900 sm:text-4xl">{{ $post->title }}</h1>
                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-ink-500">
                    @if($post->author)<span>{{ $post->author->name }}</span>@endif
                    <time datetime="{{ jdate_iso($post->published_at) }}">{{ jdate($post->published_at) }}</time>
                    <span>{{ $post->reading_minutes ?? 3 }} دقیقه مطالعه</span>
                </div>
            </header>

            @if($post->cover)
                <x-picture :media="$post->cover" size="lg" eager class="mt-8 w-full rounded-2xl" ratio="16/9" />
            @endif

            <div class="prose-fa mt-8">{!! $post->body !!}</div>

            @if($post->tags->isNotEmpty())
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="rounded-full bg-paper-100 px-3 py-1 text-xs text-ink-600 hover:bg-paper-200">#{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="section-pad bg-paper-50">
            <div class="container-site">
                <h2 class="mb-8 text-xl font-bold text-ink-900">مقالات مرتبط</h2>
                <div class="grid gap-6 sm:grid-cols-3">
                    @foreach($related as $item)
                        <x-post-card :post="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('jsonld')
<script type="application/ld+json">
{!! json_encode(array_filter([
    '@'.'context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'description' => \Illuminate\Support\Str::limit(strip_tags((string) $post->excerpt), 200),
    'image' => $post->cover?->url,
    'datePublished' => $post->published_at?->toIso8601String(),
    'dateModified' => $post->updated_at?->toIso8601String(),
    'author' => $post->author ? ['@type' => 'Person', 'name' => $post->author->name] : null,
    'publisher' => ['@type' => 'Organization', 'name' => $site->get('general.company_name', config('app.name'))],
    'mainEntityOfPage' => url()->current(),
]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
