@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        @if(!empty($s['heading']))
            <p class="mb-10 text-center text-sm font-semibold tracking-wide text-ink-400">{{ $s['heading'] }}</p>
        @endif
        <div class="grid grid-cols-2 items-center gap-x-6 gap-y-8 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($items as $customer)
                @php($logo = $customer->logo)
                <div class="flex h-14 items-center justify-center rounded-xl border border-paper-200 bg-white px-4 transition hover:border-brand-200
                            {{ ($s['grayscale'] ?? true) ? 'opacity-75 grayscale hover:opacity-100 hover:grayscale-0' : '' }}"
                     style="box-shadow: var(--shadow-soft);">
                    @if($logo)
                        <img src="{{ $logo->isSvg() ? $logo->url : $logo->variantUrl('sm') }}" alt="{{ $customer->name }}" class="max-h-9 w-auto object-contain" loading="lazy">
                    @else
                        <span class="text-sm font-bold text-ink-500">{{ $customer->name }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
