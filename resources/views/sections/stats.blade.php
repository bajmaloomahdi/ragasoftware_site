@php($s = $settings)
@if(!empty($s['items']))
<section class="section-pad">
    <div class="container-site">
        @if(!empty($s['heading']))
            <h2 class="mb-10 text-center text-2xl font-bold text-navy-900 sm:text-3xl">{{ $s['heading'] }}</h2>
        @endif
        <dl class="grid gap-8 text-center sm:grid-cols-2 lg:grid-cols-4">
            @foreach($s['items'] as $item)
                <div class="rounded-2xl bg-mist-50 p-6">
                    <dt class="text-3xl font-extrabold text-brand-600 sm:text-4xl">{{ $item['value'] ?? '' }}</dt>
                    <dd class="mt-2 text-sm text-navy-600">{{ $item['label'] ?? '' }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
@endif
