@php($s = $settings)
<section class="section-pad bg-paper-50">
    <div class="container-site grid gap-12 lg:grid-cols-2 lg:gap-16">
        <div>
            <x-section-heading :eyebrow="$s['eyebrow'] ?? 'تماس'" :title="$s['heading'] ?? 'با ما در تماس باشید'" :subtitle="$s['subheading'] ?? null" align="start" />
            @if($s['show_contact_info'] ?? true)
                <dl class="mt-10 space-y-4">
                    @foreach([
                        ['📞', 'تلفن', $site->get('contact.phone'), true],
                        ['✉️', 'ایمیل', $site->get('contact.email'), true],
                        ['📍', 'نشانی', $site->get('contact.address'), false],
                        ['🕘', 'ساعات کاری', $site->get('contact.working_hours'), false],
                    ] as [$icon, $label, $value, $ltr])
                        @if($value)
                            <div class="flex items-start gap-3">
                                <span class="icon-chip h-10 w-10 text-base">{{ $icon }}</span>
                                <div>
                                    <dt class="text-xs font-semibold text-ink-400">{{ $label }}</dt>
                                    <dd class="text-sm font-medium text-ink-800" @if($ltr) dir="ltr" @endif>{{ $value }}</dd>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </dl>
            @endif
        </div>
        <div>
            @include('partials.contact-form')
        </div>
    </div>
</section>
