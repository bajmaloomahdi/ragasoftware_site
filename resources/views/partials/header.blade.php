@php
    $logo = $site->get('general.logo_media_id') ? \App\Models\Media::find($site->get('general.logo_media_id')) : null;
    $logoLight = $site->get('general.logo_light_media_id') ? \App\Models\Media::find($site->get('general.logo_light_media_id')) : null;
    $companyName = $site->get('general.company_name', config('app.name'));
    $nav = $siteMenus->location('header');
@endphp

@if($site->get('appearance.show_top_bar') && $site->get('appearance.top_bar_text'))
    <div class="bg-navy-800 text-white">
        <div class="container-site flex items-center justify-between py-2 text-xs sm:text-sm">
            <span>{{ $site->get('appearance.top_bar_text') }}</span>
            @if($phone = $site->get('contact.phone'))
                <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="hidden font-medium hover:text-brand-300 sm:inline" dir="ltr">{{ $phone }}</a>
            @endif
        </div>
    </div>
@endif

<header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
        class="sticky top-0 z-40 border-b border-mist-200 bg-white/95 backdrop-blur transition"
        :class="scrolled && 'shadow-sm'">
    <nav class="container-site flex h-16 items-center justify-between gap-4 lg:h-20" aria-label="ناوبری اصلی">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold text-navy-900">
            @if($logo)
                <img src="{{ $logo->variantUrl('sm') }}" alt="{{ $companyName }}" class="h-9 w-auto lg:h-10" width="140" height="40">
            @else
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-500 text-white">R</span>
                <span class="text-lg lg:text-xl">{{ $companyName }}</span>
            @endif
        </a>

        {{-- Desktop nav --}}
        <ul class="hidden items-center gap-1 lg:flex">
            @foreach($nav as $item)
                <li @class(['relative group' => !empty($item['children'])])>
                    <a href="{{ $item['url'] }}" target="{{ $item['target'] }}"
                       @class([
                           'inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-navy-700 transition hover:bg-mist-100 hover:text-brand-600',
                           'bg-mist-100 text-brand-600' => request()->fullUrlIs($item['url']) || request()->is(trim(parse_url($item['url'], PHP_URL_PATH) ?? '', '/').'*') && parse_url($item['url'], PHP_URL_PATH) !== '/',
                       ])>
                        {{ $item['label'] }}
                        @if(!empty($item['children']))
                            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none"><path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5"/></svg>
                        @endif
                    </a>
                    @if(!empty($item['children']))
                        <ul class="invisible absolute right-0 top-full z-10 min-w-48 translate-y-1 rounded-xl border border-mist-200 bg-white p-2 opacity-0 shadow-lg transition group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                            @foreach($item['children'] as $child)
                                <li><a href="{{ $child['url'] }}" class="block rounded-lg px-3 py-2 text-sm text-navy-700 hover:bg-mist-100 hover:text-brand-600">{{ $child['label'] }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="hidden lg:block">
            <a href="{{ route('contact') }}" class="btn-primary">درخواست مشاوره</a>
        </div>

        {{-- Mobile toggle --}}
        <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-menu"
                class="grid h-11 w-11 place-items-center rounded-lg text-navy-800 hover:bg-mist-100 lg:hidden" aria-label="باز/بستن منو">
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </nav>

    {{-- Mobile menu --}}
    <div id="mobile-menu" x-show="open" x-collapse x-cloak class="border-t border-mist-200 lg:hidden">
        <ul class="container-site space-y-1 py-4">
            @foreach($nav as $item)
                <li>
                    <a href="{{ $item['url'] }}" class="block rounded-lg px-3 py-3 text-base font-medium text-navy-800 hover:bg-mist-100">{{ $item['label'] }}</a>
                    @if(!empty($item['children']))
                        <ul class="mr-3 space-y-1 border-r border-mist-200 pr-3">
                            @foreach($item['children'] as $child)
                                <li><a href="{{ $child['url'] }}" class="block rounded-lg px-3 py-2 text-sm text-navy-600 hover:bg-mist-100">{{ $child['label'] }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
            <li class="pt-2">
                <a href="{{ route('contact') }}" class="btn-primary w-full">درخواست مشاوره</a>
            </li>
        </ul>
    </div>
</header>
