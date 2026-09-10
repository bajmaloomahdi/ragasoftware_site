@props(['product'])

<article class="card card-hover group relative flex flex-col">
    @if($product->heroImage)
        <div class="mb-5 overflow-hidden rounded-xl border border-paper-200 bg-paper-50">
            <x-picture :media="$product->heroImage" ratio="16/10" class="w-full object-cover transition duration-500 group-hover:scale-[1.04]" />
        </div>
    @endif

    <div class="mb-4 flex items-center gap-3">
        @if($product->icon)
            <span class="icon-chip h-11 w-11 shrink-0">{{ $product->icon }}</span>
        @endif
        <div>
            <h3 class="text-lg font-bold text-ink-900 transition group-hover:text-brand-700">
                <a href="{{ route('products.show', $product->slug) }}" class="after:absolute after:inset-0">{{ $product->title }}</a>
            </h3>
            @if($product->tagline)
                <p class="text-sm text-brand-600">{{ $product->tagline }}</p>
            @endif
        </div>
    </div>

    <p class="flex-1 text-sm leading-7 text-ink-500">
        {{ \Illuminate\Support\Str::limit($product->summary, 120) }}
    </p>

    <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600">
        اطلاعات بیشتر
        <svg class="h-4 w-4 transition group-hover:-translate-x-1 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
    </span>
</article>
