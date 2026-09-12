<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo-meta', ['seo' => $seo ?? null])

    @if($gtm = $site->get('analytics.gtm_id'))
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtm }}');</script>
    @endif
    @if($ga = $site->get('analytics.ga4_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $ga }}');</script>
    @endif
    @if($gsc = $site->get('analytics.search_console_verification'))
        <meta name="google-site-verification" content="{{ $gsc }}">
    @endif
    {!! $site->get('analytics.head_scripts') !!}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen bg-white antialiased">
    @if($gtm)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-3 focus:rounded focus:bg-ink-800 focus:px-4 focus:py-2 focus:text-white">
        رفتن به محتوای اصلی
    </a>

    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')

    <button type="button"
            x-data="{ show: false }"
            @scroll.window="show = window.scrollY > 400"
            x-show="show" x-cloak x-transition.opacity
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-6 left-6 z-30 grid h-11 w-11 place-items-center rounded-full bg-white text-ink-700 transition hover:-translate-y-0.5 hover:text-brand-600"
            style="box-shadow: var(--shadow-lift);"
            aria-label="بازگشت به بالای صفحه">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7"/></svg>
    </button>

    {!! $site->get('analytics.body_scripts') !!}
    @stack('scripts')
</body>
</html>
