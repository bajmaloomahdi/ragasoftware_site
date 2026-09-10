@extends('layouts.public')

@php
    $hasHero = $page->activeSections->first()?->type === 'hero';
@endphp

@section('content')
    @unless($isHome ?? false)
        <x-breadcrumbs :items="[['label' => $page->title]]" />

        @unless($hasHero)
            <header class="bg-paper-50">
                <div class="container-site py-10 sm:py-14">
                    <h1 class="text-3xl font-extrabold text-ink-900 sm:text-4xl">{{ $page->title }}</h1>
                    @if($page->excerpt)
                        <p class="mt-3 lead max-w-2xl">{{ $page->excerpt }}</p>
                    @endif
                </div>
            </header>
        @endunless
    @endunless

    {!! $sectionsHtml !!}

    @if(! ($isHome ?? false) && $page->activeSections->isEmpty() && ! $page->excerpt)
        <div class="container-site section-pad text-ink-500">این صفحه هنوز محتوایی ندارد.</div>
    @endif
@endsection

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $page->seo?->meta_title ?: $page->title,
    'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($page->seo?->meta_description ?: $page->excerpt)), 200),
    'url' => url()->current(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
