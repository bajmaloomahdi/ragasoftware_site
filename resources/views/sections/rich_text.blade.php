@php($s = $settings)
@php($width = ['narrow' => 'max-w-2xl', 'wide' => 'max-w-5xl'][$s['width'] ?? 'normal'] ?? 'max-w-3xl')
<section class="section-pad">
    <div class="container-site">
        <div class="{{ $width }} mx-auto">
            @if(!empty($s['heading']))
                <h2 class="mb-6 text-2xl font-bold text-navy-900 sm:text-3xl">{{ $s['heading'] }}</h2>
            @endif
            <div class="prose-fa">{!! $s['body'] ?? '' !!}</div>
        </div>
    </div>
</section>
