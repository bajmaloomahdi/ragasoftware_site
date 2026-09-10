@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'تیم'" :title="$s['heading'] ?? 'تیم ما'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($items as $member)
                <div class="group text-center">
                    <div class="relative mx-auto mb-4 h-32 w-32">
                        <div class="glow -inset-2 opacity-0 transition group-hover:opacity-60" style="background: radial-gradient(circle, var(--color-brand-200), transparent 70%);"></div>
                        <div class="relative h-full w-full overflow-hidden rounded-2xl border border-paper-200 bg-paper-50">
                            <x-picture :media="$member->photo" ratio="1/1" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                        </div>
                    </div>
                    <h3 class="font-bold text-ink-900">{{ $member->name }}</h3>
                    <p class="text-sm text-brand-600">{{ $member->role_title }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
