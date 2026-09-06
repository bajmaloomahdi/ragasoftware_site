@php($s = $settings)
@if(($medias ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        @if(!empty($s['heading']))
            <x-section-heading :title="$s['heading']" />
        @endif
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($medias as $m)
                <x-picture :media="$m" ratio="4/3" class="w-full rounded-xl object-cover" />
            @endforeach
        </div>
    </div>
</section>
@endif
