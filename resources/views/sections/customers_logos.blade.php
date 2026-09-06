@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="py-12 sm:py-16">
    <div class="container-site">
        @if(!empty($s['heading']))
            <p class="mb-8 text-center text-sm font-semibold uppercase tracking-wide text-navy-400">{{ $s['heading'] }}</p>
        @endif
        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
            @foreach($items as $customer)
                @php($logo = $customer->logo)
                <div class="flex h-12 items-center {{ ($s['grayscale'] ?? true) ? 'opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0' : '' }}">
                    @if($logo)
                        <img src="{{ $logo->isSvg() ? $logo->url : $logo->variantUrl('sm') }}" alt="{{ $customer->name }}" class="max-h-12 w-auto" loading="lazy">
                    @else
                        <span class="text-sm font-bold text-navy-500">{{ $customer->name }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
