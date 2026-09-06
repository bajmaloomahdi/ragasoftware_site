@php($s = $settings)
<section class="section-pad">
    <div class="container-site grid gap-12 lg:grid-cols-2">
        <div>
            <x-section-heading :title="$s['heading'] ?? 'با ما در تماس باشید'" :subtitle="$s['subheading'] ?? null" align="start" />
            @if($s['show_contact_info'] ?? true)
                <dl class="mt-8 space-y-4 text-sm">
                    @if($p = $site->get('contact.phone'))<div><dt class="font-bold text-navy-900">تلفن</dt><dd class="text-navy-600" dir="ltr">{{ $p }}</dd></div>@endif
                    @if($e = $site->get('contact.email'))<div><dt class="font-bold text-navy-900">ایمیل</dt><dd class="text-navy-600" dir="ltr">{{ $e }}</dd></div>@endif
                    @if($a = $site->get('contact.address'))<div><dt class="font-bold text-navy-900">نشانی</dt><dd class="text-navy-600">{{ $a }}</dd></div>@endif
                    @if($h = $site->get('contact.working_hours'))<div><dt class="font-bold text-navy-900">ساعات کاری</dt><dd class="text-navy-600">{{ $h }}</dd></div>@endif
                </dl>
            @endif
        </div>
        <div>
            @include('partials.contact-form')
        </div>
    </div>
</section>
