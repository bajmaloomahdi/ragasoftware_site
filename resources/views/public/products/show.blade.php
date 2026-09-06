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
        <header class="bg-navy-800 text-white">
            <div class="container-site grid items-center gap-10 py-14 lg:grid-cols-2 lg:py-20">
                <div>
                    @if($product->category)
                        <span class="eyebrow text-brand-300">{{ $product->category->name }}</span>
                    @endif
                    <h1 class="text-3xl font-extrabold sm:text-4xl">{{ $product->title }}</h1>
                    @if($product->tagline)<p class="mt-3 text-lg text-brand-200">{{ $product->tagline }}</p>@endif
                    @if($product->summary)<p class="mt-5 max-w-xl text-navy-100">{{ $product->summary }}</p>@endif
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ $product->cta_url ?: route('contact') }}" class="btn-primary btn-lg">
                            {{ $product->cta_label ?: 'درخواست دمو' }}
                        </a>
                        <a href="#features" class="btn-lg btn border border-white/25 text-white hover:bg-white/10">قابلیت‌ها</a>
                    </div>
                </div>
                <div>
                    @if($product->heroImage)
                        <x-picture :media="$product->heroImage" size="lg" eager class="w-full rounded-2xl border border-white/10" ratio="16/10" />
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
            <section id="features" class="section-pad bg-mist-50">
                <div class="container-site">
                    <x-section-heading title="قابلیت‌های کلیدی" />
                    <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($product->activeFeatures as $feature)
                            <div>
                                @if($feature->icon)<span class="mb-3 inline-grid h-11 w-11 place-items-center rounded-lg bg-brand-50 text-brand-600 text-lg">{{ $feature->icon }}</span>@endif
                                <h3 class="text-base font-bold text-navy-900">{{ $feature->title }}</h3>
                                <p class="mt-2 text-sm leading-7 text-navy-600">{{ $feature->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if($related->isNotEmpty())
            <section class="section-pad">
                <div class="container-site">
                    <h2 class="mb-8 text-xl font-bold text-navy-900">محصولات مرتبط</h2>
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
