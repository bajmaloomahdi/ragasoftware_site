@php($s = $settings)
@php($items = collect($s['items'] ?? [])->filter(fn ($x) => filled($x['value'] ?? null)))
@if($items->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        <div class="relative overflow-hidden rounded-3xl border border-paper-200 p-8 sm:p-12"
             style="background-image: linear-gradient(135deg, var(--color-brand-50), var(--color-iris-50));">
            <div class="glow -left-16 -top-16 h-64 w-64" style="background: radial-gradient(circle, var(--color-brand-200), transparent 70%);"></div>
            @if(!empty($s['heading']))
                <h2 class="relative mb-10 text-center text-2xl font-extrabold text-ink-900 sm:text-3xl">{{ $s['heading'] }}</h2>
            @endif
            <dl class="relative grid gap-8 text-center sm:grid-cols-2 lg:grid-cols-4">
                @foreach($items as $item)
                    <div>
                        <dt class="text-4xl font-extrabold text-gradient sm:text-5xl">{{ $item['value'] }}</dt>
                        <dd class="mt-2 text-sm font-medium text-ink-600">{{ $item['label'] ?? '' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</section>
@endif
