@php
    $companyName = $site->get('general.company_name', config('app.name'));
    $logoLight = media($site->get('general.logo_light_media_id')) ?? media($site->get('general.logo_media_id'));
    $cols = [
        ['title' => $siteMenus->label('footer_1') ?? 'محصولات', 'items' => $siteMenus->location('footer_1')],
        ['title' => $siteMenus->label('footer_2') ?? 'شرکت', 'items' => $siteMenus->location('footer_2')],
        ['title' => $siteMenus->label('footer_3') ?? 'منابع', 'items' => $siteMenus->location('footer_3')],
    ];
    $legal = $siteMenus->location('legal');
    $year = jdate(now(), 'Y');
@endphp

<footer class="mt-auto bg-navy-800 text-navy-100">
    <div class="container-site grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-5 lg:py-16">
        <div class="lg:col-span-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white">
                @if($logoLight)
                    <img src="{{ $logoLight->variantUrl('sm') }}" alt="{{ $companyName }}" class="h-9 w-auto" width="140" height="40">
                @else
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-500 font-extrabold text-white">R</span>
                    <span class="text-lg font-extrabold">{{ $companyName }}</span>
                @endif
            </a>
            <p class="mt-4 max-w-sm text-sm leading-7 text-navy-200">
                {{ $site->get('footer.about_text') }}
            </p>

            @if($socialLinks->isNotEmpty())
                <div class="mt-5 flex gap-2">
                    @foreach($socialLinks as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener nofollow"
                           title="{{ $link->label ?: $link->platform }}"
                           class="grid h-10 w-10 place-items-center rounded-lg bg-white/10 text-white transition hover:bg-brand-500">
                            <span class="text-xs">{{ mb_substr($link->platform, 0, 2) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @foreach($cols as $col)
            @if(!empty($col['items']))
                <nav aria-label="{{ $col['title'] }}">
                    <h2 class="mb-4 text-sm font-bold text-white">{{ $col['title'] }}</h2>
                    <ul class="space-y-2 text-sm">
                        @foreach($col['items'] as $item)
                            <li><a href="{{ $item['url'] }}" class="text-navy-200 transition hover:text-white">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        @endforeach

        <div>
            <h2 class="mb-4 text-sm font-bold text-white">تماس</h2>
            <ul class="space-y-2 text-sm text-navy-200">
                @if($phone = $site->get('contact.phone'))<li><a href="tel:{{ preg_replace('/\s+/','',$phone) }}" dir="ltr" class="hover:text-white">{{ $phone }}</a></li>@endif
                @if($email = $site->get('contact.email'))<li><a href="mailto:{{ $email }}" dir="ltr" class="hover:text-white">{{ $email }}</a></li>@endif
                @if($addr = $site->get('contact.address'))<li>{{ $addr }}</li>@endif
                @if($hours = $site->get('contact.working_hours'))<li class="text-navy-300">{{ $hours }}</li>@endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-site flex flex-col items-center justify-between gap-3 py-5 text-xs text-navy-300 sm:flex-row">
            <p>© {{ $year }} — {{ $site->get('footer.copyright') }}</p>
            @if(!empty($legal))
                <ul class="flex flex-wrap gap-4">
                    @foreach($legal as $item)
                        <li><a href="{{ $item['url'] }}" class="hover:text-white">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</footer>
