@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad bg-mist-50">
    <div class="container-site">
        <x-section-heading :title="$s['heading'] ?? 'تیم ما'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($items as $member)
                <div class="text-center">
                    <div class="mx-auto mb-4 h-28 w-28 overflow-hidden rounded-full bg-white">
                        <x-picture :media="$member->photo" ratio="1/1" class="h-full w-full object-cover" />
                    </div>
                    <h3 class="font-bold text-navy-900">{{ $member->name }}</h3>
                    <p class="text-sm text-navy-500">{{ $member->role_title }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
