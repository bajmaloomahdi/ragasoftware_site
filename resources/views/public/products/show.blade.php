@extends('layouts.public')
@php
    $title = $product->seo?->meta_title ?: $product->title;
    $description = $product->seo?->meta_description ?: $product->summary;
    $ogType = 'product';
@endphp

@section('content')
    <x-breadcrumbs :items="[
        ['label' => 'محصولات', 'url' => route('products.index')],
        ['label' => $product->title],
    ]" />

    <article>
        <header class="relative overflow-hidden bg-white">
            <div class="glow -left-40 -top-32 h-[26rem] w-[26rem]" style="background: radial-gradient(circle, var(--color-iris-200), transparent 70%);"></div>
            <div class="glow -right-32 top-10 h-[24rem] w-[24rem]" style="background: radial-gradient(circle, var(--color-brand-200), transparent 70%);"></div>
            <div class="container-site relative z-10 grid items-center gap-10 py-14 lg:grid-cols-2 lg:py-20">
                <div>
                    @if($product->category)
                        <span class="eyebrow"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>{{ $product->category->name }}</span>
                    @endif
                    <h1 class="text-3xl font-extrabold text-ink-900 sm:text-4xl lg:text-[2.8rem]">{{ $product->title }}</h1>
                    @if($product->tagline)<p class="mt-3 text-lg font-medium text-brand-600">{{ $product->tagline }}</p>@endif
                    @if($product->summary)<p class="mt-5 max-w-xl text-ink-500">{{ $product->summary }}</p>@endif
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a href="{{ $product->cta_url ?: route('contact') }}" class="btn-primary btn-lg">
                            {{ $product->cta_label ?: 'درخواست دمو' }}
                            <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="#features" class="btn-ghost btn-lg">قابلیت‌ها</a>
                    </div>
                </div>
                <div>
                    @if($product->heroImage)
                        <div class="rounded-3xl border border-paper-200 bg-white p-2" style="box-shadow: var(--shadow-lift);">
                            <x-picture :media="$product->heroImage" size="lg" eager class="w-full rounded-2xl" ratio="16/10" />
                        </div>
                    @endif
                </div>
            </div>
        </header>

        @if($product->body)
            <div class="section-pad">
                <div class="container-site max-w-3xl prose-fa">{!! $product->body !!}</div>
            </div>
        @endif

        @if($product->activeFeatures->isNotEmpty())
            <section id="features" class="section-pad bg-paper-50">
                <div class="container-site">
                    <x-section-heading title="قابلیت‌های کلیدی" />
                    <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($product->activeFeatures as $feature)
                            <div>
                                <span class="icon-chip h-11 w-11">{{ $feature->icon ?: '✓' }}</span>
                                <h3 class="mt-4 text-base font-bold text-ink-900">{{ $feature->title }}</h3>
                                <p class="mt-2 text-sm leading-7 text-ink-500">{{ $feature->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if($related->isNotEmpty())
            <section class="section-pad">
                <div class="container-site">
                    <h2 class="mb-8 text-xl font-bold text-ink-900">محصولات مرتبط</h2>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($related as $item)
                            <x-product-card :product="$item" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @include('partials.page-cta')
    </article>
@endsection

@push('jsonld')
<script type="application/ld+json">
{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->title,
    'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($product->summary ?: $product->body)), 300),
    'brand' => ['@type' => 'Brand', 'name' => $site->get('general.company_name', config('app.name'))],
    'image' => $product->heroImage?->url,
    'url' => url()->current(),
]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
