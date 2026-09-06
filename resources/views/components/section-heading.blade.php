@props(['eyebrow' => null, 'title', 'subtitle' => null, 'align' => 'center', 'as' => 'h2'])

<div @class([
    'max-w-2xl',
    'mx-auto text-center' => $align === 'center',
])>
    @if($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
    <{{ $as }} class="text-2xl font-bold text-navy-900 sm:text-3xl lg:text-4xl" style="text-wrap:balance">
        {{ $title }}
    </{{ $as }}>
    @if($subtitle)
        <p class="mt-4 lead">{{ $subtitle }}</p>
    @endif
    {{ $slot }}
</div>
