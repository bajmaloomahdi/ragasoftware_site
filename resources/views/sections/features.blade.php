@php($s = $settings)
@php($cols = ['2' => 'sm:grid-cols-2', '4' => 'sm:grid-cols-2 lg:grid-cols-4'][$s['columns'] ?? '3'] ?? 'sm:grid-cols-2 lg:grid-cols-3')
@php($items = collect($s['items'] ?? [])->filter(fn ($x) => filled($x['title'] ?? null)))
@if($items->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        <x-section-heading :title="$s['heading'] ?? 'ویژگی‌ها'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-14 grid gap-x-8 gap-y-10 {{ $cols }}">
            @foreach($items as $item)
                <div>
                    <span class="icon-chip h-11 w-11">{{ $item['icon'] ?? '✓' }}</span>
                    <h3 class="mt-4 font-bold text-ink-900">{{ $item['title'] ?? '' }}</h3>
                    <p class="mt-2 text-sm leading-7 text-ink-500">{{ $item['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
