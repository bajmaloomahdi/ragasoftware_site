@extends('layouts.public')
@php($title = 'نمونه‌کارها')

@section('content')
    <x-breadcrumbs :items="[['label' => 'نمونه‌کارها']]" />
    <div class="container-site section-pad">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-extrabold text-navy-900 sm:text-4xl">نمونه‌کارها و پروژه‌ها</h1>
            <p class="mt-4 lead">پروژه‌هایی که با راگا سافت‌ور به نتیجه رسیده‌اند.</p>
        </div>

        @if($projects->isEmpty())
            <p class="mt-12 text-navy-500">به‌زودی…</p>
        @else
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($projects as $project)
                    <a href="{{ route('projects.show', $project->slug) }}" class="group overflow-hidden rounded-2xl border border-mist-200 bg-white">
                        <x-picture :media="$project->cover" ratio="4/3" class="w-full object-cover transition duration-300 group-hover:scale-[1.04]" />
                        <div class="p-5">
                            <p class="text-xs font-medium text-brand-600">{{ $project->client_name ?: optional($project->customer)->name }}</p>
                            <h2 class="mt-1 font-bold text-navy-900">{{ $project->title }}</h2>
                            @if($project->summary)<p class="mt-2 text-sm text-navy-600">{{ \Illuminate\Support\Str::limit($project->summary, 90) }}</p>@endif
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-10">{{ $projects->links() }}</div>
        @endif
    </div>
    @include('partials.page-cta')
@endsection
