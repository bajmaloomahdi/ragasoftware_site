@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad bg-paper-50">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'خدمات'" :title="$s['heading'] ?? 'خدمات ما'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $service)
                <a href="{{ route('services.show', $service->slug) }}" class="card card-hover group flex flex-col">
                    <span class="icon-chip mb-5 h-12 w-12 text-xl">{{ $service->icon ?: '✳️' }}</span>
                    <h3 class="text-lg font-bold text-ink-900 transition group-hover:text-brand-700">{{ $service->title }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-7 text-ink-500">{{ \Illuminate\Support\Str::limit($service->summary, 110) }}</p>
                    <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600">
                        جزئیات خدمت
                        <svg class="h-4 w-4 transition group-hover:-translate-x-1 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
        <div class="mt-12 text-center">
            <a href="{{ route('services.index') }}" class="btn-ghost">همه خدمات</a>
        </div>
    </div>
</section>
@endif
