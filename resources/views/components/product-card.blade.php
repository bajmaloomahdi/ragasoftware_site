@props(['product'])

<article class="card card-hover group relative flex flex-col">
    @if($product->heroImage)
        <a href="{{ route('products.show', $product->slug) }}" class="mb-4 block overflow-hidden rounded-xl">
            <x-picture :media="$product->heroImage" ratio="16/10" class="w-full object-cover transition duration-300 group-hover:scale-[1.03]" />
        </a>
    @endif

    @if($product->icon)
        <span class="mb-3 inline-grid h-11 w-11 place-items-center rounded-lg bg-brand-50 text-brand-600 text-lg">
            {{ $product->icon }}
        </span>
    @endif

    <h3 class="text-lg font-bold text-navy-900">
        <a href="{{ route('products.show', $product->slug) }}" class="after:absolute after:inset-0">
            {{ $product->title }}
        </a>
    </h3>

    @if($product->tagline)
        <p class="mt-1 text-sm font-medium text-brand-600">{{ $product->tagline }}</p>
    @endif

    <p class="mt-3 flex-1 text-sm leading-7 text-navy-600">
        {{ \Illuminate\Support\Str::limit($product->summary, 130) }}
    </p>

    <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">
        اطلاعات بیشتر
        <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
    </span>
</article>
