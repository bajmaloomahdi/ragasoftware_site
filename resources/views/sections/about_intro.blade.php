@php($s = $settings)
<section class="section-pad">
    <div class="container-site grid items-center gap-12 lg:grid-cols-2">
        <div class="order-2 lg:order-1">
            @if(!empty($s['heading']))
                <h2 class="text-2xl font-bold text-navy-900 sm:text-3xl">{{ $s['heading'] }}</h2>
            @endif
            @if(!empty($s['body']))
                <div class="prose-fa mt-5 max-w-xl">{!! $s['body'] !!}</div>
            @endif
            @if(!empty($s['cta_label']))
                <a href="{{ $s['cta_url'] ?? '#' }}" class="btn-ghost mt-6">{{ $s['cta_label'] }}</a>
            @endif
        </div>
        <div class="order-1 lg:order-2">
            <x-picture :media="$media ?? null" ratio="4/3" class="w-full rounded-2xl object-cover" />
        </div>
    </div>
</section>
