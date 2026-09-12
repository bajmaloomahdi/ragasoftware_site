@php
    use Illuminate\Support\Str;
    $logo = media($site->get('general.logo_media_id'));
    $companyName = $site->get('general.company_name', config('app.name'));
    $nav = $siteMenus->location('header');
    $spy = fn ($url) => str_contains((string) $url, '#') ? Str::after($url, '#') : null;
@endphp

@if($site->get('appearance.show_top_bar') && $site->get('appearance.top_bar_text'))
    <div class="text-white" style="background-image: linear-gradient(90deg, var(--color-brand-600), var(--color-iris-600));">
        <div class="container-site flex items-center justify-between py-2 text-xs sm:text-sm">
            <span>{{ $site->get('appearance.top_bar_text') }}</span>
            @if($phone = $site->get('contact.phone'))
                <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="hidden font-semibold hover:text-white/80 sm:inline" dir="ltr">{{ $phone }}</a>
            @endif
        </div>
    </div>
@endif

<header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
        class="sticky top-0 z-40 bg-white transition-shadow"
        :class="scrolled ? 'border-b border-paper-200 shadow-[0_10px_30px_-20px_rgba(20,22,29,0.35)]' : 'border-b border-paper-100'">
    <nav class="container-site flex h-16 items-center justify-between gap-4 lg:h-[4.5rem]" aria-label="ناوبری اصلی">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold text-ink-900">
            @if($logo)
                <img src="{{ $logo->variantUrl('sm') }}" alt="{{ $companyName }}" class="img-fade h-9 w-auto lg:h-10" width="140" height="40">
            @else
                <span class="grid h-9 w-9 place-items-center rounded-xl text-sm text-white" style="background-image: linear-gradient(135deg, var(--color-brand-500), var(--color-iris-500));">R</span>
                <span class="text-lg lg:text-xl">{{ $companyName }}</span>
            @endif
        </a>

        {{-- Desktop nav --}}
        <ul class="hidden items-center gap-0.5 lg:flex">
            @foreach($nav as $item)
                @php($frag = $spy($item['url']))
                <li @class(['relative group' => !empty($item['children'])])>
                    <a href="{{ $item['url'] }}" target="{{ $item['target'] }}"
                       @if($frag) data-spy="{{ $frag }}" @endif
                       @class([
                           'nav-link inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-ink-600 transition hover:bg-paper-100 hover:text-ink-900',
                           'is-active' => ! $frag && request()->fullUrlIs($item['url']),
                       ])>
                        {{ $item['label'] }}
                        @if(!empty($item['children']))
                            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none"><path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.6"/></svg>
                        @endif
                    </a>
                    @if(!empty($item['children']))
                        <ul class="invisible absolute right-0 top-full z-10 min-w-48 translate-y-1 rounded-2xl border border-paper-200 bg-white p-2 opacity-0 shadow-lg transition group-hover:visible group-hover:translate-y-1.5 group-hover:opacity-100">
                            @foreach($item['children'] as $child)
                                <li><a href="{{ $child['url'] }}" class="block rounded-lg px-3 py-2 text-sm text-ink-600 hover:bg-paper-100 hover:text-brand-700">{{ $child['label'] }}</a></li>
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
                class="grid h-11 w-11 place-items-center rounded-xl text-ink-800 hover:bg-paper-100 lg:hidden" aria-label="باز/بستن منو">
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </nav>

    {{-- Mobile menu --}}
    <div id="mobile-menu" x-show="open" x-collapse x-cloak class="border-t border-paper-200 bg-white lg:hidden">
        <ul class="container-site space-y-1 py-4">
            @foreach($nav as $item)
                <li>
                    <a href="{{ $item['url'] }}" @click="open = false" class="block rounded-xl px-3 py-3 text-base font-medium text-ink-800 hover:bg-paper-100">{{ $item['label'] }}</a>
                    @if(!empty($item['children']))
                        <ul class="mr-3 space-y-1 border-r-2 border-paper-200 pr-3">
                            @foreach($item['children'] as $child)
                                <li><a href="{{ $child['url'] }}" @click="open = false" class="block rounded-lg px-3 py-2 text-sm text-ink-500 hover:bg-paper-100">{{ $child['label'] }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
            <li class="pt-2">
                <a href="{{ route('contact') }}" @click="open = false" class="btn-primary w-full">درخواست مشاوره</a>
            </li>
        </ul>
    </div>
</header>
