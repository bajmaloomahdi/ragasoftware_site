@props([
    'media' => null,
    'alt' => null,
    'size' => 'lg',
    'class' => '',
    'sizes' => '(max-width: 768px) 100vw, 768px',
    'eager' => false,
    'ratio' => null, // e.g. "16/9"
])

@php
    /** @var \App\Models\Media|null $media */
    $altText = $alt ?? ($media?->alt_text) ?? '';
    $style = $ratio ? "aspect-ratio: {$ratio};" : '';
@endphp

@if($media)
    @if($media->isSvg())
        <img src="{{ $media->url }}" alt="{{ $altText }}" {{ $attributes->merge(['class' => $class]) }}
             @if(!$eager) loading="lazy" decoding="async" @endif style="{{ $style }}">
    @else
        <picture>
            @if($media->srcset(true))
                <source type="image/webp" srcset="{{ $media->srcset(true) }}" sizes="{{ $sizes }}">
            @endif
            @if($media->srcset())
                <source srcset="{{ $media->srcset() }}" sizes="{{ $sizes }}">
            @endif
            <img src="{{ $media->variantUrl($size) }}"
                 alt="{{ $altText }}"
                 width="{{ $media->width ?: 1200 }}" height="{{ $media->height ?: 800 }}"
                 {{ $attributes->merge(['class' => $class]) }}
                 @if($eager) fetchpriority="high" @else loading="lazy" decoding="async" @endif
                 style="{{ $style }}">
        </picture>
    @endif
@else
    <div {{ $attributes->merge(['class' => trim($class.' grid place-items-center bg-paper-100 text-ink-300')]) }} style="{{ $style ?: 'aspect-ratio: 16/10;' }}">
        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M3 16l5-5 4 4 3-3 6 6M3 6h18v12H3z"/></svg>
    </div>
@endif
