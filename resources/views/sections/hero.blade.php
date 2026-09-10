@php($s = $settings)
@php($cards = ($logoCards ?? collect())->take(3))
@php($stats = collect($s['stats'] ?? [])->filter(fn ($x) => filled($x['value'] ?? null))->take(3))

<section class="relative overflow-hidden bg-white">
    {{-- soft ambient background --}}
    <div class="glow -right-32 -top-40 h-[28rem] w-[28rem]" style="background: radial-gradient(circle, var(--color-brand-200), transparent 70%);"></div>
    <div class="glow -left-40 top-24 h-[26rem] w-[26rem]" style="background: radial-gradient(circle, var(--color-iris-200), transparent 70%);"></div>
    <div class="pointer-events-none absolute inset-0 opacity-40"
         style="background-image: radial-gradient(circle at 1px 1px, var(--color-paper-300) 1px, transparent 0); background-size: 28px 28px; -webkit-mask-image: linear-gradient(to bottom, black, transparent 78%); mask-image: linear-gradient(to bottom, black, transparent 78%);"></div>

    <div class="container-site relative z-10 grid items-center gap-14 py-16 sm:py-20 lg:grid-cols-12 lg:gap-8 lg:py-28">

        {{-- Text (right in RTL) --}}
        <div class="lg:col-span-6">
            @if(!empty($s['eyebrow']))
                <span class="eyebrow">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                    {{ $s['eyebrow'] }}
                </span>
            @endif

            <h1 class="mt-4 text-[2rem] font-extrabold leading-[1.18] text-ink-900 xs:text-4xl sm:text-5xl lg:text-[3.4rem]" style="text-wrap:balance">
                {{ $s['heading'] ?? '' }}
            </h1>

            <div class="mt-6 h-1 w-20 rounded-full" style="background-image: linear-gradient(90deg, var(--color-brand-500), var(--color-iris-500));"></div>

            @if(!empty($s['subheading']))
                <p class="mt-6 max-w-xl text-base leading-8 text-ink-500 sm:text-lg">{{ $s['subheading'] }}</p>
            @endif

            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                @if(!empty($s['primary_cta_label']))
                    <a href="{{ $s['primary_cta_url'] ?: '#contact' }}" class="btn-primary btn-lg">
                        {{ $s['primary_cta_label'] }}
                        <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
                @if(!empty($s['secondary_cta_label']))
                    <a href="{{ $s['secondary_cta_url'] ?: '#products' }}" class="btn-ghost btn-lg">{{ $s['secondary_cta_label'] }}</a>
                @endif
            </div>

            @if($stats->isNotEmpty())
                <dl class="mt-12 flex flex-wrap items-center gap-x-8 gap-y-4 border-t border-paper-200 pt-7">
                    @foreach($stats as $stat)
                        <div class="flex items-baseline gap-2">
                            <dt class="text-2xl font-extrabold text-ink-900">{{ $stat['value'] }}</dt>
                            <dd class="text-sm text-ink-500">{{ $stat['label'] ?? '' }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>

        {{-- Logo cards composition (left in RTL) --}}
        <div class="lg:col-span-6">
            @if($cards->isNotEmpty())
                <div class="group relative mx-auto flex max-w-sm items-stretch justify-center gap-3 sm:max-w-lg sm:gap-5">
                    <div class="glow inset-x-4 inset-y-6" style="background: radial-gradient(ellipse, var(--color-brand-100), transparent 72%);"></div>

                    @foreach($cards as $i => $logo)
                        @php($offset = ['sm:translate-y-6 sm:-rotate-2', 'sm:-translate-y-4 sm:rotate-1', 'sm:translate-y-10 sm:-rotate-1'][$i] ?? '')
                        <div class="relative flex-1 transition duration-500 ease-out {{ $offset }} hover:z-10 hover:!translate-y-0 hover:!rotate-0">
                            <div class="flex aspect-[3/4.4] flex-col items-center justify-center rounded-2xl border border-paper-200 bg-white p-3 transition duration-500 hover:-translate-y-1.5 hover:border-brand-200 sm:p-4"
                                 style="box-shadow: var(--shadow-soft);">
                                <span class="absolute inset-x-0 top-0 h-1 rounded-t-2xl opacity-0 transition duration-500 group-hover:opacity-100"
                                      style="background-image: linear-gradient(90deg, var(--color-brand-500), var(--color-iris-500));"></span>
                                <div class="grid flex-1 place-items-center">
                                    <img src="{{ $logo->isSvg() ? $logo->url : $logo->variantUrl('sm') }}"
                                         alt="{{ $logo->alt_text ?: 'لوگوی مشتری' }}"
                                         class="max-h-14 w-auto max-w-[85%] object-contain opacity-90 transition duration-500 hover:opacity-100 sm:max-h-16"
                                         loading="eager" width="120" height="64">
                                </div>
                                <span class="mt-2 h-1 w-7 rounded-full bg-paper-200"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($media ?? null)
                <div class="relative mx-auto max-w-lg rounded-2xl border border-paper-200 bg-white p-2" style="box-shadow: var(--shadow-lift);">
                    <x-picture :media="$media" size="lg" eager class="w-full rounded-xl" ratio="16/10" />
                </div>
            @endif
        </div>
    </div>
</section>
