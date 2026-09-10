@php($s = $settings)
@php($style = $s['style'] ?? 'gradient')
<section class="section-pad">
    <div class="container-site">
        <div @class([
                'relative isolate overflow-hidden rounded-3xl px-6 py-16 text-center sm:px-12 lg:py-20',
                'text-white' => $style !== 'light',
                'border border-paper-200 bg-white text-ink-900' => $style === 'light',
                'bg-ink-900' => $style === 'solid',
             ])
             @if($style === 'gradient') style="background-image: linear-gradient(130deg, var(--color-brand-600), var(--color-iris-600));" @endif>

            @if($style !== 'light')
                <div class="glow -right-20 -top-24 h-72 w-72 opacity-40" style="background: radial-gradient(circle, #ffffff, transparent 70%);"></div>
                <div class="glow -left-24 bottom-0 h-72 w-72 opacity-30" style="background: radial-gradient(circle, var(--color-iris-300), transparent 70%);"></div>
            @endif

            <h2 class="relative mx-auto max-w-2xl text-2xl font-extrabold sm:text-3xl lg:text-[2.4rem]" style="text-wrap:balance">
                {{ $s['heading'] ?? '' }}
            </h2>
            @if(!empty($s['subheading']))
                <p class="relative mx-auto mt-4 max-w-xl {{ $style === 'light' ? 'text-ink-500' : 'text-white/85' }}">{{ $s['subheading'] }}</p>
            @endif
            @if(!empty($s['cta_label']))
                <a href="{{ $s['cta_url'] ?: route('contact') }}"
                   class="relative mt-9 {{ $style === 'light' ? 'btn-primary' : 'btn-white' }} btn-lg">
                    {{ $s['cta_label'] }}
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
    </div>
</section>
