@extends('layouts.public')
@php($title = 'خدمات')

@section('content')
    <x-breadcrumbs :items="[['label' => 'خدمات']]" />
    <div class="container-site section-pad">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-extrabold text-navy-900 sm:text-4xl">خدمات راگا سافت‌ور</h1>
            <p class="mt-4 lead">از مشاوره و پیاده‌سازی تا توسعه اختصاصی و پشتیبانی؛ کنار سازمان شما در تمام مسیر.</p>
        </div>

        @if($services->isEmpty())
            <p class="mt-12 text-navy-500">به‌زودی…</p>
        @else
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($services as $service)
                    <a href="{{ route('services.show', $service->slug) }}" class="card card-hover group flex flex-col">
                        @if($service->icon)<span class="mb-4 inline-grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-brand-600 text-xl">{{ $service->icon }}</span>@endif
                        <h2 class="text-lg font-bold text-navy-900 group-hover:text-brand-600">{{ $service->title }}</h2>
                        <p class="mt-2 flex-1 text-sm leading-7 text-navy-600">{{ \Illuminate\Support\Str::limit($service->summary, 130) }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    @include('partials.page-cta')
@endsection
