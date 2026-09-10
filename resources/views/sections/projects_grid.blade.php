@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad bg-paper-50">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'نمونه‌کارها'" :title="$s['heading'] ?? 'پروژه‌هایی که به نتیجه رسیدند'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $project)
                <a href="{{ route('projects.show', $project->slug) }}" class="card card-hover group relative flex flex-col overflow-hidden p-0">
                    <div class="overflow-hidden border-b border-paper-200 bg-white">
                        <x-picture :media="$project->cover" ratio="4/3" class="w-full object-cover transition duration-500 group-hover:scale-[1.05]" />
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <p class="text-xs font-semibold text-brand-600">{{ $project->client_name ?: optional($project->customer)->name }}</p>
                        <h3 class="mt-1 font-bold text-ink-900 transition group-hover:text-brand-700">{{ $project->title }}</h3>
                        @if($project->summary)
                            <p class="mt-2 text-sm leading-7 text-ink-500">{{ \Illuminate\Support\Str::limit($project->summary, 90) }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-12 text-center">
            <a href="{{ route('projects.index') }}" class="btn-ghost">همه نمونه‌کارها</a>
        </div>
    </div>
</section>
@endif
