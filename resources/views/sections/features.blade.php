@php($s = $settings)
@php($cols = ['2' => 'sm:grid-cols-2', '4' => 'sm:grid-cols-2 lg:grid-cols-4'][$s['columns'] ?? '3'] ?? 'sm:grid-cols-2 lg:grid-cols-3')
<section class="section-pad">
    <div class="container-site">
        <x-section-heading :title="$s['heading'] ?? 'ویژگی‌ها'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-12 grid gap-8 {{ $cols }}">
            @foreach(($s['items'] ?? []) as $item)
                <div>
                    @if(!empty($item['icon']))
                        <span class="mb-3 inline-grid h-11 w-11 place-items-center rounded-lg bg-brand-50 text-brand-600 text-lg">{{ $item['icon'] }}</span>
                    @endif
                    <h3 class="text-base font-bold text-navy-900">{{ $item['title'] ?? '' }}</h3>
                    <p class="mt-2 text-sm leading-7 text-navy-600">{{ $item['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
