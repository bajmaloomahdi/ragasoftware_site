@php($s = $settings)
<section class="section-pad bg-navy-800 text-white">
    <div class="container-site grid gap-12 lg:grid-cols-2">
        <div>
            @if(!empty($s['heading']))
                <h2 class="text-2xl font-bold sm:text-3xl lg:text-4xl">{{ $s['heading'] }}</h2>
            @endif
            @if(!empty($s['subheading']))
                <p class="mt-4 max-w-md text-navy-100">{{ $s['subheading'] }}</p>
            @endif
            @if($media ?? null)
                <div class="mt-8 overflow-hidden rounded-2xl border border-white/10">
                    <x-picture :media="$media" ratio="4/3" class="w-full object-cover" />
                </div>
            @endif
        </div>
        <div class="space-y-6">
            @foreach(($s['items'] ?? []) as $item)
                <div class="flex gap-4">
                    <span class="mt-1 grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-brand-500 text-white">
                        {{ $item['icon'] ?? '✓' }}
                    </span>
                    <div>
                        <h3 class="font-bold text-white">{{ $item['title'] ?? '' }}</h3>
                        <p class="mt-1 text-sm leading-7 text-navy-200">{{ $item['description'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
