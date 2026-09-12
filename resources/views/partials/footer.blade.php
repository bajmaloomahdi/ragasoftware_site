@php
    $companyName = $site->get('general.company_name', config('app.name'));
    $logo = media($site->get('general.logo_media_id'));
    $cols = [
        ['title' => $siteMenus->label('footer_1') ?? 'محصولات', 'items' => $siteMenus->location('footer_1')],
        ['title' => $siteMenus->label('footer_2') ?? 'شرکت', 'items' => $siteMenus->location('footer_2')],
        ['title' => $siteMenus->label('footer_3') ?? 'منابع', 'items' => $siteMenus->location('footer_3')],
    ];
    $legal = $siteMenus->location('legal');
    $year = jdate(now(), 'Y');
@endphp

<footer class="relative mt-auto border-t border-paper-200 bg-white">
    <div class="h-px w-full" style="background-image: linear-gradient(90deg, transparent, var(--color-brand-300), var(--color-iris-300), transparent);"></div>

    <div class="container-site grid gap-12 py-16 sm:grid-cols-2 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @if($logo)
                    <img src="{{ $logo->variantUrl('sm') }}" alt="{{ $companyName }}" class="img-fade h-9 w-auto" width="140" height="40">
                @else
                    <span class="grid h-9 w-9 place-items-center rounded-xl text-sm font-extrabold text-white" style="background-image: linear-gradient(135deg, var(--color-brand-500), var(--color-iris-500));">R</span>
                    <span class="text-lg font-extrabold text-ink-900">{{ $companyName }}</span>
                @endif
            </a>
            <p class="mt-4 max-w-sm text-sm leading-7 text-ink-500">{{ $site->get('footer.about_text') }}</p>

            @if($socialLinks->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach($socialLinks as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener nofollow"
                           title="{{ $link->label ?: $link->platform }}"
                           class="grid h-10 w-10 place-items-center rounded-xl border border-paper-200 bg-white text-xs font-bold text-ink-500 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">
                            {{ mb_substr($link->label ?: $link->platform, 0, 2) }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @foreach($cols as $col)
            @if(!empty($col['items']))
                <nav aria-label="{{ $col['title'] }}">
                    <h2 class="mb-4 text-sm font-bold text-ink-900">{{ $col['title'] }}</h2>
                    <ul class="space-y-2.5 text-sm">
                        @foreach($col['items'] as $item)
                            <li><a href="{{ $item['url'] }}" class="text-ink-500 transition hover:text-brand-600">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        @endforeach

        <div>
            <h2 class="mb-4 text-sm font-bold text-ink-900">تماس</h2>
            <ul class="space-y-2.5 text-sm text-ink-500">
                @if($phone = $site->get('contact.phone'))<li><a href="tel:{{ preg_replace('/\s+/','',$phone) }}" dir="ltr" class="hover:text-brand-600">{{ $phone }}</a></li>@endif
                @if($email = $site->get('contact.email'))<li><a href="mailto:{{ $email }}" dir="ltr" class="hover:text-brand-600">{{ $email }}</a></li>@endif
                @if($addr = $site->get('contact.address'))<li class="leading-7">{{ $addr }}</li>@endif
                @if($hours = $site->get('contact.working_hours'))<li class="text-ink-400">{{ $hours }}</li>@endif
            </ul>
        </div>
    </div>

    <div class="border-t border-paper-200">
        <div class="container-site flex flex-col items-center justify-between gap-3 py-6 text-xs text-ink-400 sm:flex-row">
            <p>© {{ $year }} — {{ $site->get('footer.copyright') }}</p>
            @if(!empty($legal))
                <ul class="flex flex-wrap gap-4">
                    @foreach($legal as $item)
                        <li><a href="{{ $item['url'] }}" class="hover:text-brand-600">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</footer>
