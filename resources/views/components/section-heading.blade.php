@props(['eyebrow' => null, 'title', 'subtitle' => null, 'align' => 'center', 'as' => 'h2'])

<div @class([
    'max-w-2xl',
    'mx-auto text-center' => $align === 'center',
])>
    @if($eyebrow)
        <span class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>{{ $eyebrow }}</span>
    @endif
    <{{ $as }} class="text-2xl font-extrabold leading-tight text-ink-900 sm:text-3xl lg:text-[2.4rem]" style="text-wrap:balance">
        {{ $title }}
    </{{ $as }}>
    @if($align === 'center')
        <div class="mx-auto mt-4 h-1 w-14 rounded-full" style="background-image: linear-gradient(90deg, var(--color-brand-500), var(--color-iris-500));"></div>
    @endif
    @if($subtitle)
        <p class="mt-4 lead">{{ $subtitle }}</p>
    @endif
    {{ $slot }}
</div>
