@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site max-w-3xl">
        <x-section-heading :title="$s['heading'] ?? 'سؤالات متداول'" />
        <div class="mt-10 divide-y divide-mist-200 border-y border-mist-200" x-data="{ open: null }">
            @foreach($items as $i => $faq)
                <div>
                    <h3>
                        <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                                class="flex w-full items-center justify-between gap-4 py-5 text-right text-base font-semibold text-navy-900"
                                :aria-expanded="open === {{ $i }}">
                            <span>{{ $faq->question }}</span>
                            <svg class="h-5 w-5 shrink-0 text-brand-500 transition" :class="open === {{ $i }} && 'rotate-45'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </h3>
                    <div x-show="open === {{ $i }}" x-collapse x-cloak>
                        <div class="prose-fa pb-5 text-sm">{!! $faq->answer !!}</div>
                    </div>
                </div>
            @endforeach
        </div>

        @push('jsonld')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items->map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f->answer)],
            ])->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        @endpush
    </div>
</section>
@endif
