@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad bg-mist-50">
    <div class="container-site">
        <x-section-heading :eyebrow="'نمونه‌کارها'" :title="$s['heading'] ?? 'پروژه‌های اجراشده'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $project)
                <a href="{{ route('projects.show', $project->slug) }}" class="group relative overflow-hidden rounded-2xl border border-mist-200 bg-white">
                    <x-picture :media="$project->cover" ratio="4/3" class="w-full object-cover transition duration-300 group-hover:scale-[1.04]" />
                    <div class="p-5">
                        <p class="text-xs font-medium text-brand-600">{{ $project->client_name ?: optional($project->customer)->name }}</p>
                        <h3 class="mt-1 font-bold text-navy-900">{{ $project->title }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('projects.index') }}" class="btn-ghost">همه نمونه‌کارها</a>
        </div>
    </div>
</section>
@endif
