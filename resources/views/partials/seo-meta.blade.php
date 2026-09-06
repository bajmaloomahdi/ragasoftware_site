{{-- Rendered from layouts/public with the $seo SeoData DTO (see SeoResolver). --}}
<title>{{ $seo->title }}</title>
<meta name="description" content="{{ $seo->description }}">
<meta name="robots" content="{{ $seo->robots }}">
<link rel="canonical" href="{{ $seo->canonical }}">

<meta property="og:type" content="{{ $seo->ogType }}">
<meta property="og:site_name" content="{{ $site->get('general.company_name', config('app.name')) }}">
<meta property="og:title" content="{{ $seo->ogTitle ?? $seo->title }}">
<meta property="og:description" content="{{ $seo->ogDescription ?? $seo->description }}">
<meta property="og:url" content="{{ $seo->canonical }}">
@if($seo->ogImage)<meta property="og:image" content="{{ url($seo->ogImage) }}">@endif
<meta property="og:locale" content="{{ config('seo.defaults.locale', 'fa_IR') }}">
<meta name="twitter:card" content="{{ $seo->twitterCard }}">
<meta name="twitter:title" content="{{ $seo->ogTitle ?? $seo->title }}">
<meta name="twitter:description" content="{{ $seo->ogDescription ?? $seo->description }}">
@if($seo->ogImage)<meta name="twitter:image" content="{{ url($seo->ogImage) }}">@endif

@if($fav = media($site->get('general.favicon_media_id')))
    <link rel="icon" href="{{ $fav->url }}">
@endif

@foreach($seo->jsonLd as $block)
    <script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach
@stack('jsonld')
