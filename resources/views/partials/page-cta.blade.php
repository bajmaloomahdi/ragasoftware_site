<section class="section-pad">
    <div class="container-site">
        <div class="relative isolate overflow-hidden rounded-3xl px-6 py-16 text-center text-white sm:px-12 lg:py-20"
             style="background-image: linear-gradient(130deg, var(--color-brand-600), var(--color-iris-600));">
            <div class="glow -right-20 -top-24 h-72 w-72 opacity-40" style="background: radial-gradient(circle, #ffffff, transparent 70%);"></div>
            <div class="glow -left-24 bottom-0 h-72 w-72 opacity-30" style="background: radial-gradient(circle, var(--color-iris-300), transparent 70%);"></div>

            <h2 class="relative mx-auto max-w-2xl text-2xl font-extrabold sm:text-3xl lg:text-[2.4rem]" style="text-wrap:balance">
                {{ $ctaHeading ?? 'آماده‌اید کسب‌وکار خود را هوشمندتر مدیریت کنید؟' }}
            </h2>
            <p class="relative mx-auto mt-4 max-w-xl text-white/85">
                {{ $ctaText ?? 'کارشناسان راگا سافت‌ور آماده‌اند تا نیاز سازمان شما را بررسی و بهترین راهکار را پیشنهاد دهند.' }}
            </p>
            <a href="{{ route('contact') }}" class="btn-white btn-lg relative mt-9">
                درخواست مشاوره رایگان
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
