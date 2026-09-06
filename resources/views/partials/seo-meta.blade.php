{{-- Basic SEO meta. Phase 7 expands this with the SeoResolver DTO + JSON-LD. --}}
@php
    $seo = $seo ?? null;
    $metaTitle = $seo?->title ?? ($title ?? null) ?? $site->get('seo.default_title', config('app.name'));
    $metaDesc = $seo?->description ?? ($description ?? null) ?? $site->get('seo.default_description', '');
    $canonical = $seo?->canonical ?? url()->current();
    $robots = $seo?->robots ?? 'index, follow';
    $ogImage = $seo?->ogImage ?? null;
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDesc), 160) }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:site_name" content="{{ $site->get('general.company_name', config('app.name')) }}">
<meta property="og:title" content="{{ $seo?->ogTitle ?? $metaTitle }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seo?->ogDescription ?? $metaDesc), 200) }}">
<meta property="og:url" content="{{ $canonical }}">
@if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
<meta name="twitter:card" content="{{ $seo?->twitterCard ?? 'summary_large_image' }}">

@if($favicon = $site->get('general.favicon_media_id'))
    @php($fav = \App\Models\Media::find($favicon))
    @if($fav)<link rel="icon" href="{{ $fav->url }}">@endif
@endif

@stack('jsonld')
