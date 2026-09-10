@php($s = $settings)
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
        <div class="relative order-1 lg:order-2">
            <div class="glow -inset-4" style="background: radial-gradient(circle, var(--color-iris-100), transparent 70%);"></div>
            <div class="relative overflow-hidden rounded-3xl border border-paper-200 bg-white p-2" style="box-shadow: var(--shadow-soft);">
                <x-picture :media="$media ?? null" ratio="4/3" class="w-full rounded-2xl object-cover" />
            </div>
        </div>
    </div>
</section>
