@props(['items' => []])
{{-- $items: [ ['label' => '...', 'url' => '...'], ... ] ; last item is current --}}

@if(count($items))
<nav aria-label="مسیر" class="border-b border-paper-200 bg-paper-50">
    <ol class="container-site flex flex-wrap items-center gap-2 py-3 text-sm text-ink-500">
        <li><a href="{{ route('home') }}" class="hover:text-brand-600">خانه</a></li>
        @foreach($items as $i => $item)
            <li aria-hidden="true" class="text-ink-300">/</li>
            <li>
                @if(!$loop->last && !empty($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-brand-600">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-ink-700" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

@push('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($items)->prepend(['label' => 'خانه', 'url' => route('home')])
        ->values()
        ->map(fn ($item, $idx) => array_filter([
            '@type' => 'ListItem',
            'position' => $idx + 1,
            'name' => $item['label'],
            'item' => $item['url'] ?? null,
        ]))->all(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
@endif
