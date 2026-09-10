@php($s = $settings)
@if(($medias ?? collect())->isNotEmpty())
<section class="py-14">
    <div class="container-site">
        @if(!empty($s['heading']))
            <p class="mb-10 text-center text-sm font-semibold tracking-wide text-ink-400">{{ $s['heading'] }}</p>
        @endif
        <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-8">
            @foreach($medias as $m)
                <img src="{{ $m->isSvg() ? $m->url : $m->variantUrl('sm') }}" alt="{{ $m->alt_text }}" class="h-9 w-auto opacity-60 grayscale transition hover:opacity-100 hover:grayscale-0" loading="lazy">
            @endforeach
        </div>
    </div>
</section>
@endif
