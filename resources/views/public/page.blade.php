@extends('layouts.public')

@php
    $title = $page->seo?->meta_title ?: $page->title;
    $description = $page->seo?->meta_description ?: $page->excerpt;
@endphp

@section('content')
    @unless($isHome ?? false)
        <x-breadcrumbs :items="[['label' => $page->title]]" />
        @if(!$page->sections()->exists())
            <div class="container-site section-pad">
                <h1 class="text-3xl font-bold text-navy-900 sm:text-4xl">{{ $page->title }}</h1>
                @if($page->excerpt)<p class="mt-4 lead max-w-2xl">{{ $page->excerpt }}</p>@endif
            </div>
        @endif
    @endunless

    {!! $sectionsHtml !!}
@endsection

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $title,
    'description' => \Illuminate\Support\Str::limit(strip_tags((string) $description), 200),
    'url' => url()->current(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
