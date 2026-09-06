@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        <x-section-heading :title="$s['heading'] ?? 'مشتریان ما چه می‌گویند'" />
        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $t)
                <figure class="card flex h-full flex-col">
                    <blockquote class="flex-1 text-sm leading-8 text-navy-700">«{{ $t->body }}»</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3 border-t border-mist-200 pt-4">
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-brand-50 font-bold text-brand-600">
                            {{ mb_substr($t->author_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-navy-900">{{ $t->author_name }}</p>
                            <p class="text-xs text-navy-500">{{ $t->author_title ?: optional($t->customer)->name }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
