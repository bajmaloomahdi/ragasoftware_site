@php($s = $settings)
@php($logo = media($site->get('general.logo_media_id')))
<section class="section-pad">
    <div class="container-site grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
        <div class="order-2 lg:order-1">
            @if(!empty($s['heading']))
                <span class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>معرفی راگا سافت‌ور</span>
                <h2 class="text-2xl font-extrabold text-ink-900 sm:text-3xl lg:text-[2.2rem]">{{ $s['heading'] }}</h2>
            @endif
            @if(!empty($s['body']))
                <div class="prose-fa mt-5 max-w-xl">{!! $s['body'] !!}</div>
            @endif
            @if(!empty($s['cta_label']))
                <a href="{{ $s['cta_url'] ?: '#' }}" class="btn-ghost mt-7">
                    {{ $s['cta_label'] }}
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
        {{-- Company logo showcase box --}}
        <div class="relative order-1 lg:order-2">
            <div class="glow -inset-4" style="background: radial-gradient(circle, var(--color-iris-100), transparent 70%);"></div>
            <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden rounded-3xl border border-paper-200 bg-white p-10"
                 style="box-shadow: var(--shadow-soft);">
                <div class="pointer-events-none absolute inset-0 opacity-60"
                     style="background-image: radial-gradient(circle at 1px 1px, var(--color-paper-300) 1px, transparent 0); background-size: 24px 24px; -webkit-mask-image: radial-gradient(circle, black, transparent 75%); mask-image: radial-gradient(circle, black, transparent 75%);"></div>
                @if($logo)
                    <img src="{{ $logo->isSvg() ? $logo->url : $logo->variantUrl('lg') }}"
                         alt="{{ $site->get('general.company_name', 'راگا سافت‌ور') }}"
                         class="img-fade relative z-10 max-h-24 w-auto max-w-[70%] object-contain sm:max-h-32"
                         loading="lazy" decoding="async" width="240" height="96">
                @else
                    <span class="relative z-10 text-2xl font-extrabold text-ink-300">{{ $site->get('general.company_name', 'راگا سافت‌ور') }}</span>
                @endif
            </div>
        </div>
    </div>
</section>
