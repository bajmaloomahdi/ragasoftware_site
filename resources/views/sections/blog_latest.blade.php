@php($s = $settings)
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'وبلاگ'" :title="$s['heading'] ?? 'مقالات منتخب'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $post)
                <x-post-card :post="$post" />
            @endforeach
        </div>
        <div class="mt-12 text-center">
            <a href="{{ route('blog.index') }}" class="btn-ghost">همه مقالات</a>
        </div>
    </div>
</section>
@endif
