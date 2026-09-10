@extends('layouts.public')
@php
    $title = $page?->seo?->meta_title ?: 'تماس با ما';
    $description = $page?->seo?->meta_description ?: $site->get('seo.default_description');
@endphp

@section('content')
    <x-breadcrumbs :items="[['label' => 'تماس با ما']]" />

    @if($sectionsHtml && $page->sections()->exists())
        {!! $sectionsHtml !!}
    @else
        <section class="section-pad">
            <div class="container-site grid gap-12 lg:grid-cols-2">
                <div>
                    <h1 class="text-3xl font-extrabold text-ink-900 sm:text-4xl">با ما در تماس باشید</h1>
                    <p class="mt-4 lead">برای مشاوره، دمو یا همکاری، فرم را پر کنید یا مستقیماً با ما تماس بگیرید.</p>

                    <dl class="mt-8 space-y-4 text-sm">
                        @if($p = $site->get('contact.phone'))<div><dt class="font-bold text-ink-900">تلفن</dt><dd class="text-ink-600" dir="ltr">{{ $p }}</dd></div>@endif
                        @if($e = $site->get('contact.email'))<div><dt class="font-bold text-ink-900">ایمیل</dt><dd class="text-ink-600" dir="ltr">{{ $e }}</dd></div>@endif
                        @if($a = $site->get('contact.address'))<div><dt class="font-bold text-ink-900">نشانی</dt><dd class="text-ink-600">{{ $a }}</dd></div>@endif
                        @if($h = $site->get('contact.working_hours'))<div><dt class="font-bold text-ink-900">ساعات کاری</dt><dd class="text-ink-600">{{ $h }}</dd></div>@endif
                    </dl>

                    @if($map = $site->get('contact.map_embed'))
                        <div class="mt-8 overflow-hidden rounded-2xl border border-paper-200">{!! $map !!}</div>
                    @endif
                </div>
                <div>
                    @include('partials.contact-form')
                </div>
            </div>
        </section>
    @endif
@endsection

@push('jsonld')
<script type="application/ld+json">
{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $site->get('general.company_name', config('app.name')),
    'url' => url('/'),
    'email' => $site->get('contact.email'),
    'telephone' => $site->get('contact.phone'),
    'address' => $site->get('contact.address') ? ['@type' => 'PostalAddress', 'streetAddress' => $site->get('contact.address')] : null,
    'sameAs' => $socialLinks->pluck('url')->all(),
]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
