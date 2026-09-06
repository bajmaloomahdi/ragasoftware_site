<section class="section-pad">
    <div class="container-site">
        <div class="rounded-3xl bg-navy-800 px-6 py-14 text-center text-white sm:px-12 lg:py-16">
            <h2 class="mx-auto max-w-2xl text-2xl font-extrabold sm:text-3xl">
                {{ $ctaHeading ?? 'آماده‌اید کسب‌وکار خود را هوشمندتر مدیریت کنید؟' }}
            </h2>
            <p class="mx-auto mt-3 max-w-xl text-white/80">
                {{ $ctaText ?? 'کارشناسان راگا سافت‌ور آماده‌اند تا نیاز سازمان شما را بررسی و بهترین راهکار را پیشنهاد دهند.' }}
            </p>
            <a href="{{ route('contact') }}" class="btn-white btn-lg mt-8">درخواست مشاوره رایگان</a>
        </div>
    </div>
</section>
