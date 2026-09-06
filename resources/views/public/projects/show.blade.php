@extends('layouts.public')
@php
    $title = $project->seo?->meta_title ?: $project->title;
    $description = $project->seo?->meta_description ?: $project->summary;
@endphp

@section('content')
    <x-breadcrumbs :items="[
        ['label' => 'نمونه‌کارها', 'url' => route('projects.index')],
        ['label' => $project->title],
    ]" />

    <article class="section-pad">
        <div class="container-site max-w-3xl">
            <p class="text-sm font-semibold text-brand-600">{{ $project->client_name ?: optional($project->customer)->name }}</p>
            <h1 class="mt-2 text-3xl font-extrabold text-navy-900 sm:text-4xl">{{ $project->title }}</h1>
            @if($project->summary)<p class="mt-4 lead">{{ $project->summary }}</p>@endif

            @if($project->cover)
                <x-picture :media="$project->cover" size="lg" class="mt-8 w-full rounded-2xl" ratio="16/9" />
            @endif

            @if($project->completed_on || $project->project_url)
                <dl class="mt-6 flex flex-wrap gap-x-10 gap-y-2 border-y border-mist-200 py-4 text-sm">
                    @if($project->completed_on)
                        <div><dt class="text-navy-400">تاریخ اجرا</dt><dd class="font-medium text-navy-800">{{ jdate($project->completed_on, 'F Y') }}</dd></div>
                    @endif
                    @if($project->project_url)
                        <div><dt class="text-navy-400">لینک</dt><dd><a href="{{ $project->project_url }}" target="_blank" rel="noopener" class="font-medium text-brand-600" dir="ltr">{{ $project->project_url }}</a></dd></div>
                    @endif
                </dl>
            @endif

            @if($project->body)<div class="prose-fa mt-8">{!! $project->body !!}</div>@endif

            @if($gallery->isNotEmpty())
                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    @foreach($gallery as $img)
                        <x-picture :media="$img" ratio="4/3" class="w-full rounded-xl object-cover" />
                    @endforeach
                </div>
            @endif
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="section-pad bg-mist-50">
            <div class="container-site">
                <h2 class="mb-8 text-xl font-bold text-navy-900">پروژه‌های دیگر</h2>
                <div class="grid gap-6 sm:grid-cols-3">
                    @foreach($related as $item)
                        <a href="{{ route('projects.show', $item->slug) }}" class="group overflow-hidden rounded-2xl border border-mist-200 bg-white">
                            <x-picture :media="$item->cover" ratio="4/3" class="w-full object-cover" />
                            <div class="p-4"><h3 class="font-bold text-navy-900">{{ $item->title }}</h3></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
