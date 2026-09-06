@php($s = $settings)
@php($cols = ['2' => 'sm:grid-cols-2', '4' => 'sm:grid-cols-2 lg:grid-cols-4'][$s['columns'] ?? '3'] ?? 'sm:grid-cols-2 lg:grid-cols-3')
@if(($items ?? collect())->isNotEmpty())
<section class="section-pad bg-mist-50">
    <div class="container-site">
        <x-section-heading :eyebrow="$s['eyebrow'] ?? 'محصولات و راهکارها'" :title="$s['heading'] ?? 'محصولات راگا سافت‌ور'" :subtitle="$s['subheading'] ?? null" />
        <div class="mt-12 grid gap-6 {{ $cols }}">
            @foreach($items as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('products.index') }}" class="btn-ghost">مشاهده همه محصولات</a>
        </div>
    </div>
</section>
@endif
