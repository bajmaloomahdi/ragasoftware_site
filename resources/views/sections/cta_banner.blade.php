@php($s = $settings)
@php($style = $s['style'] ?? 'navy')
<section class="section-pad">
    <div class="container-site">
        <div @class([
            'relative overflow-hidden rounded-3xl px-6 py-14 text-center sm:px-12 lg:py-20',
            'bg-navy-800 text-white' => $style === 'navy',
            'bg-mist-50 text-navy-900 border border-mist-200' => $style === 'light',
            'bg-gradient-to-br from-brand-600 to-cyan-600 text-white' => $style === 'gradient',
        ])>
            <h2 class="mx-auto max-w-2xl text-2xl font-extrabold sm:text-3xl lg:text-4xl" style="text-wrap:balance">
                {{ $s['heading'] ?? '' }}
            </h2>
            @if(!empty($s['subheading']))
                <p class="mx-auto mt-4 max-w-xl {{ $style === 'light' ? 'text-navy-600' : 'text-white/85' }}">{{ $s['subheading'] }}</p>
            @endif
            @if(!empty($s['cta_label']))
                <a href="{{ $s['cta_url'] ?? route('contact') }}" class="{{ $style === 'light' ? 'btn-primary' : 'btn-white' }} btn-lg mt-8">
                    {{ $s['cta_label'] }}
                </a>
            @endif
        </div>
    </div>
</section>
