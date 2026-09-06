@php($s = $settings)
@if(($medias ?? collect())->isNotEmpty())
<section class="py-12">
    <div class="container-site">
        @if(!empty($s['heading']))
            <p class="mb-8 text-center text-sm font-semibold uppercase tracking-wide text-navy-400">{{ $s['heading'] }}</p>
        @endif
        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
            @foreach($medias as $m)
                <img src="{{ $m->isSvg() ? $m->url : $m->variantUrl('sm') }}" alt="{{ $m->alt_text }}" class="h-8 w-auto opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0" loading="lazy">
            @endforeach
        </div>
    </div>
</section>
@endif
