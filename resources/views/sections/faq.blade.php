@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad bg-paper-50">
    <div class="container-site max-w-3xl">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'سؤالات متداول'" :title="$s['heading'] ?? 'سؤالات متداول'" />
        <div class="mt-12 space-y-3" x-data="{ open: 0 }">
            @foreach($items as $i => $faq)
                <div class="overflow-hidden rounded-2xl border border-paper-200 bg-white transition"
                     :class="open === {{ $i }} && 'border-brand-200'"
                     style="box-shadow: var(--shadow-soft);">
                    <h3>
                        <button type="button" @click="open = (open === {{ $i }} ? null : {{ $i }})"
                                class="flex w-full items-center justify-between gap-4 px-5 py-4 text-right text-base font-bold text-ink-900"
                                :aria-expanded="open === {{ $i }}">
                            <span>{{ $faq->question }}</span>
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-600 transition"
                                  :class="open === {{ $i }} && 'rotate-45 bg-brand-500 text-white'">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                            </span>
                        </button>
                    </h3>
                    <div x-show="open === {{ $i }}" x-collapse x-cloak>
                        <div class="prose-fa border-t border-paper-200 px-5 py-4 text-sm">{!! $faq->answer !!}</div>
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
