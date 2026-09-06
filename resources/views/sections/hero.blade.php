@php($s = $settings)
<section class="relative overflow-hidden bg-navy-800 text-white">
    <div class="pointer-events-none absolute inset-0 opacity-[0.15]"
         style="background-image: radial-gradient(circle at 20% 20%, #2563eb 0, transparent 40%), radial-gradient(circle at 90% 10%, #06b6d4 0, transparent 35%);"></div>

    <div class="container-site relative grid items-center gap-12 py-16 sm:py-24 lg:grid-cols-2 lg:py-28">
        <div>
            @if(!empty($s['eyebrow']))
                <span class="mb-4 inline-block rounded-full border border-white/20 px-3 py-1 text-xs font-semibold tracking-wide">
                    {{ $s['eyebrow'] }}
                </span>
            @endif

            <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl lg:text-5xl" style="text-wrap:balance">
                {{ $s['heading'] ?? '' }}
            </h1>

            @if(!empty($s['subheading']))
                <p class="mt-5 max-w-xl text-base leading-8 text-navy-100 sm:text-lg">{{ $s['subheading'] }}</p>
            @endif

            <div class="mt-8 flex flex-wrap gap-3">
                @if(!empty($s['primary_cta_label']))
                    <a href="{{ $s['primary_cta_url'] ?? '#' }}" class="btn-primary btn-lg">{{ $s['primary_cta_label'] }}</a>
                @endif
                @if(!empty($s['secondary_cta_label']))
                    <a href="{{ $s['secondary_cta_url'] ?? '#' }}" class="btn-lg btn border border-white/25 text-white hover:bg-white/10">
                        {{ $s['secondary_cta_label'] }}
                    </a>
                @endif
            </div>

            @if(!empty($s['stats']) && is_array($s['stats']))
                <dl class="mt-12 grid max-w-lg grid-cols-3 gap-6 border-t border-white/10 pt-8">
                    @foreach($s['stats'] as $stat)
                        <div>
                            <dt class="text-2xl font-extrabold text-white">{{ $stat['value'] ?? '' }}</dt>
                            <dd class="mt-1 text-xs text-navy-200">{{ $stat['label'] ?? '' }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>

        <div class="relative">
            @if($media ?? null)
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-2 shadow-2xl">
                    <x-picture :media="$media" size="lg" eager class="w-full rounded-xl" ratio="16/10" />
                </div>
            @else
                <div class="aspect-[16/10] rounded-2xl border border-white/10 bg-gradient-to-br from-white/10 to-white/5"></div>
            @endif
        </div>
    </div>
</section>
