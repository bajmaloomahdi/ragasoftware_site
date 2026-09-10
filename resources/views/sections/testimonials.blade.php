@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad bg-paper-50">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'نظر مشتریان'" :title="$s['heading'] ?? 'مشتریان ما چه می‌گویند'" />
        <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $t)
                <figure class="card flex h-full flex-col">
                    <svg class="h-8 w-8 text-brand-200" viewBox="0 0 24 24" fill="currentColor"><path d="M9.5 5C6.5 6.5 5 9 5 12v7h6v-7H7.5c0-2 .8-3.6 2.6-4.5L9.5 5zm9 0c-3 1.5-4.5 4-4.5 7v7h6v-7h-3.5c0-2 .8-3.6 2.6-4.5L18.5 5z"/></svg>
                    <blockquote class="mt-3 flex-1 leading-8 text-ink-700">{{ $t->body }}</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3 border-t border-paper-200 pt-5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full text-sm font-extrabold text-white"
                             style="background-image: linear-gradient(135deg, var(--color-brand-500), var(--color-iris-500));">
                            {{ mb_substr($t->author_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-ink-900">{{ $t->author_name }}</p>
                            <p class="text-xs text-ink-500">{{ $t->author_title ?: optional($t->customer)->name }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
