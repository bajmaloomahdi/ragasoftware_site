@extends('layouts.public')
@php($title = 'سؤالات متداول')

@section('content')
    <x-breadcrumbs :items="[['label' => 'سؤالات متداول']]" />

    <div class="container-site section-pad max-w-3xl">
        <h1 class="text-3xl font-extrabold text-ink-900 sm:text-4xl">سؤالات متداول</h1>
        <p class="mt-4 lead">پاسخ پرسش‌های رایج درباره محصولات، پیاده‌سازی و پشتیبانی راگا سافت‌ور.</p>

        @php($allFaqs = collect())

        @foreach($categories as $category)
            <h2 class="mt-12 text-xl font-bold text-ink-900">{{ $category->name }}</h2>
            <div class="mt-4 divide-y divide-paper-200 border-y border-paper-200" x-data="{ open: null }">
                @foreach($category->faqs as $faq)
                    @php($allFaqs->push($faq))
                    <div>
                        <button type="button" @click="open === {{ $faq->id }} ? open = null : open = {{ $faq->id }}"
                                class="flex w-full items-center justify-between gap-4 py-5 text-right text-base font-semibold text-ink-900" :aria-expanded="open === {{ $faq->id }}">
                            <span>{{ $faq->question }}</span>
                            <svg class="h-5 w-5 shrink-0 text-brand-500 transition" :class="open === {{ $faq->id }} && 'rotate-45'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                        <div x-show="open === {{ $faq->id }}" x-collapse x-cloak><div class="prose-fa pb-5 text-sm">{!! $faq->answer !!}</div></div>
                    </div>
                @endforeach
            </div>
        @endforeach

        @if($uncategorised->isNotEmpty())
            <div class="mt-12 divide-y divide-paper-200 border-y border-paper-200" x-data="{ open: null }">
                @foreach($uncategorised as $faq)
                    @php($allFaqs->push($faq))
                    <div>
                        <button type="button" @click="open === {{ $faq->id }} ? open = null : open = {{ $faq->id }}"
                                class="flex w-full items-center justify-between gap-4 py-5 text-right text-base font-semibold text-ink-900" :aria-expanded="open === {{ $faq->id }}">
                            <span>{{ $faq->question }}</span>
                            <svg class="h-5 w-5 shrink-0 text-brand-500 transition" :class="open === {{ $faq->id }} && 'rotate-45'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                        <div x-show="open === {{ $faq->id }}" x-collapse x-cloak><div class="prose-fa pb-5 text-sm">{!! $faq->answer !!}</div></div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-14 rounded-2xl bg-paper-50 p-8 text-center">
            <p class="font-bold text-ink-900">پاسخ سؤال‌تان را پیدا نکردید؟</p>
            <a href="{{ route('contact') }}" class="btn-primary mt-4">تماس با ما</a>
        </div>
    </div>
@endsection

@push('jsonld')
@if($categories->isNotEmpty() || $uncategorised->isNotEmpty())
<script type="application/ld+json">
{!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $categories->flatMap->faqs->concat($uncategorised)->map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f->question,
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f->answer)],
    ])->values()->all(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif
@endpush
