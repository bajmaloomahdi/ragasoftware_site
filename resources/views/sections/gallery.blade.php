@php($s = $settings)
@if(($medias ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        @if(!empty($s['heading']))
            <x-section-heading :title="$s['heading']" />
        @endif
        <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($medias as $m)
                <div class="overflow-hidden rounded-2xl border border-paper-200 bg-paper-50" style="box-shadow: var(--shadow-soft);">
                    <x-picture :media="$m" ratio="4/3" class="w-full object-cover transition duration-500 hover:scale-[1.03]" />
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
