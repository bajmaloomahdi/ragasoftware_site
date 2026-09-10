@php($s = $settings)
@php($items = collect($s['items'] ?? [])->filter(fn ($x) => filled($x['title'] ?? null)))
<section class="section-pad bg-paper-50">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'چرا راگا سافت‌ور؟'" :title="$s['heading'] ?? 'چرا راگا سافت‌ور؟'" :subtitle="$s['subheading'] ?? null" />

        @if($items->isNotEmpty())
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                    <div class="group card card-hover">
                        <span class="icon-chip h-12 w-12 text-xl">{{ $item['icon'] ?? '✓' }}</span>
                        <h3 class="mt-4 font-bold text-ink-900">{{ $item['title'] ?? '' }}</h3>
                        <p class="mt-2 text-sm leading-7 text-ink-500">{{ $item['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        @if($media ?? null)
            <div class="mt-12 overflow-hidden rounded-3xl border border-paper-200 bg-white p-2" style="box-shadow: var(--shadow-soft);">
                <x-picture :media="$media" ratio="21/9" class="w-full rounded-2xl object-cover" />
            </div>
        @endif
    </div>
</section>
