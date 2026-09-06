@extends('layouts.public')
@php
    $title = $service->seo?->meta_title ?: $service->title;
    $description = $service->seo?->meta_description ?: $service->summary;
@endphp

@section('content')
    <x-breadcrumbs :items="[
        ['label' => 'خدمات', 'url' => route('services.index')],
        ['label' => $service->title],
    ]" />

    <article class="section-pad">
        <div class="container-site grid gap-12 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h1 class="text-3xl font-extrabold text-navy-900 sm:text-4xl">{{ $service->title }}</h1>
                @if($service->summary)<p class="mt-4 lead">{{ $service->summary }}</p>@endif
                @if($service->image)
                    <x-picture :media="$service->image" size="lg" class="mt-8 w-full rounded-2xl" ratio="16/9" />
                @endif
                @if($service->body)<div class="prose-fa mt-8">{!! $service->body !!}</div>@endif

                @if($service->features->isNotEmpty())
                    <ul class="mt-8 space-y-3">
                        @foreach($service->features as $f)
                            <li class="flex gap-3">
                                <svg class="mt-1 h-5 w-5 shrink-0 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <p class="font-semibold text-navy-900">{{ $f->title }}</p>
                                    @if($f->description)<p class="text-sm text-navy-600">{{ $f->description }}</p>@endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <aside class="lg:col-span-1">
                <div class="sticky top-24 rounded-2xl border border-mist-200 bg-mist-50 p-6">
                    <h2 class="text-base font-bold text-navy-900">درخواست این خدمت</h2>
                    <p class="mt-2 text-sm text-navy-600">برای دریافت مشاوره و برآورد، با ما تماس بگیرید.</p>
                    <a href="{{ route('contact') }}" class="btn-primary mt-4 w-full">تماس با کارشناسان</a>
                </div>
            </aside>
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="section-pad bg-mist-50">
            <div class="container-site">
                <h2 class="mb-8 text-xl font-bold text-navy-900">سایر خدمات</h2>
                <div class="grid gap-6 sm:grid-cols-3">
                    @foreach($related as $item)
                        <a href="{{ route('services.show', $item->slug) }}" class="card card-hover">
                            <h3 class="font-bold text-navy-900">{{ $item->title }}</h3>
                            <p class="mt-2 text-sm text-navy-600">{{ \Illuminate\Support\Str::limit($item->summary, 90) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
