@extends('layouts.public')
@php
    $title = 'محصولات و راهکارها';
    $description = $site->get('seo.default_description');
@endphp

@section('content')
    <x-breadcrumbs :items="[['label' => 'محصولات']]" />

    <div class="container-site section-pad">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-extrabold text-navy-900 sm:text-4xl">محصولات و راهکارهای راگا سافت‌ور</h1>
            <p class="mt-4 lead">
                مجموعه نرم‌افزارهای یکپارچه راگا برای پوشش نیازهای مدیریت سازمان، ارتباط با مشتری،
                اتوماسیون فرایندها و تصمیم‌گیری مبتنی بر داده.
            </p>
        </div>

        @if($products->isEmpty())
            <p class="mt-12 text-navy-500">به‌زودی محصولات اضافه می‌شوند.</p>
        @else
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </div>

    @include('partials.page-cta')
@endsection
