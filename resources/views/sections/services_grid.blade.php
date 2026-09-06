@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'خدمات'" :title="$s['heading'] ?? 'خدمات ما'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $service)
                <a href="{{ route('services.show', $service->slug) }}" class="card card-hover group flex flex-col">
                    @if($service->icon)
                        <span class="mb-4 inline-grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-brand-600 text-xl">{{ $service->icon }}</span>
                    @endif
                    <h3 class="text-lg font-bold text-navy-900 group-hover:text-brand-600">{{ $service->title }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-7 text-navy-600">{{ \Illuminate\Support\Str::limit($service->summary, 120) }}</p>
                </a>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('services.index') }}" class="btn-ghost">همه خدمات</a>
        </div>
    </div>
</section>
@endif
