<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Customer;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\MenuItem;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\Support\SeedMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealisticContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->social();
        $products = $this->products();
        $this->services();
        $customers = $this->customers();
        $this->testimonials($customers);
        $this->team();
        $this->blog();
        $this->projects();
        $this->faqs();
        $this->menus($products);
        $this->pages();
        $this->homepage();
    }

    private function set(string $key, string $group, string $type, string $label, mixed $value): void
    {
        SiteSetting::updateOrCreate(['key' => $key], [
            'group' => $group, 'type' => $type, 'label' => $label,
            'value' => is_bool($value) ? ($value ? '1' : '0') : $value,
        ]);
    }

    private function settings(): void
    {
        foreach (config('site.fields') as $f) {
            if (array_key_exists('default', $f)) {
                $this->set($f['key'], $f['group'], $f['type'], $f['label'], $f['default']);
            }
        }

        $og = SeedMedia::make('og-default', 'راگا سافت‌ور — نرم‌افزار سازمانی', 'og');
        $this->set('seo.default_og_media_id', 'seo', 'media', 'تصویر پیش‌فرض', (string) $og->id);
        $logo = SeedMedia::make('logo-raga', 'RAGA', 'logo');
        $this->set('general.logo_media_id', 'general', 'media', 'لوگو', (string) $logo->id);
        $this->set('general.logo_light_media_id', 'general', 'media', 'لوگوی روشن', (string) $logo->id);

        $this->set('contact.phone', 'contact', 'string', 'تلفن', '۰۲۱-۹۱۰۰۲۰۳۰');
        $this->set('contact.email', 'contact', 'string', 'ایمیل', 'info@ragasoftware.ir');
        $this->set('contact.sales_email', 'contact', 'string', 'ایمیل فروش', 'sales@ragasoftware.ir');
        $this->set('contact.address', 'contact', 'text', 'نشانی', 'تهران، خیابان ولیعصر، بالاتر از میدان ونک، برج نگار، طبقه ۹');
        $this->set('contact.working_hours', 'contact', 'string', 'ساعات کاری', 'شنبه تا چهارشنبه، ۹:۰۰ تا ۱۷:۳۰');
    }

    private function social(): void
    {
        $links = [
            ['linkedin', 'https://www.linkedin.com/company/ragasoftware'],
            ['instagram', 'https://instagram.com/ragasoftware'],
            ['telegram', 'https://t.me/ragasoftware'],
            ['aparat', 'https://www.aparat.com/ragasoftware'],
        ];
        foreach ($links as $i => [$platform, $url]) {
            SocialLink::updateOrCreate(['url' => $url], [
                'platform' => $platform, 'label' => ucfirst($platform), 'sort_order' => $i, 'is_active' => true,
            ]);
        }
    }

    /** @return \Illuminate\Support\Collection<int, Product> */
    private function products()
    {
        $author = User::first();

        $cats = collect([
            'مدیریت سازمان' => 'erp',
            'فروش و مشتری' => 'crm',
            'بهره‌وری' => 'productivity',
        ])->map(fn ($slug, $name) => ProductCategory::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'sort_order' => 0],
        ));

        $data = [
            [
                'erp-raga', 'راگا ERP', 'مدیریت یکپارچه منابع سازمان', 'مدیریت سازمان', '📊',
                'راگا ERP همه‌ی فرایندهای مالی، انبار، خرید، فروش و منابع انسانی را در یک سامانه یکپارچه می‌کند تا مدیران با یک منبع واحدِ حقیقت تصمیم بگیرند.',
                [
                    ['حسابداری و خزانه‌داری', 'دفتر کل، معین، تفصیلی، مغایرت‌گیری بانکی و گزارش‌های قانونی مطابق استانداردهای ایران.'],
                    ['مدیریت انبار و کالا', 'کنترل موجودی چنداَنباره، شماره‌سری و بچ، انبارگردانی و قیمت‌گذاری میانگین/فایفو.'],
                    ['خرید و تدارکات', 'درخواست خرید، استعلام، سفارش خرید و تطبیق سه‌جانبه فاکتور، رسید و سفارش.'],
                    ['منابع انسانی و حقوق‌ودستمزد', 'پرسنلی، کارکرد، محاسبه حقوق، بیمه و مالیات و فیش حقوقی الکترونیک.'],
                    ['بهای تمام‌شده', 'محاسبه بهای تمام‌شده تولید بر مبنای مراکز هزینه و سفارش کار.'],
                ],
            ],
            [
                'crm-raga', 'راگا CRM', 'ارتباط با مشتری، از سرنخ تا وفاداری', 'فروش و مشتری', '🤝',
                'راگا CRM چرخه کامل فروش را از جذب سرنخ تا خدمات پس از فروش مدیریت می‌کند و تصویری ۳۶۰ درجه از هر مشتری می‌سازد.',
                [
                    ['مدیریت سرنخ و فرصت', 'قیف فروش قابل‌تنظیم، امتیازدهی سرنخ و پیش‌بینی فروش.'],
                    ['اتوماسیون فروش', 'وظایف خودکار، یادآوری پیگیری و گردش‌کار تأیید پیش‌فاکتور.'],
                    ['باشگاه مشتریان', 'امتیاز، سطح‌بندی و کمپین‌های هدفمند پیامکی و ایمیلی.'],
                    ['میز خدمت (تیکتینگ)', 'ثبت، تخصیص و پایش درخواست‌های پشتیبانی با SLA.'],
                ],
            ],
            [
                'automation-raga', 'اتوماسیون اداری راگا', 'گردش مکاتبات و فرایندها بدون کاغذ', 'بهره‌وری', '📨',
                'با اتوماسیون اداری راگا، نامه‌ها، فرم‌ها و فرایندهای سازمانی به‌صورت الکترونیک و قابل‌ردیابی جریان پیدا می‌کنند.',
                [
                    ['دبیرخانه و مکاتبات', 'ثبت وارده/صادره، ارجاع، پاراف و بایگانی دیجیتال با امضای الکترونیک.'],
                    ['فرم‌ساز و فرایندساز', 'طراحی فرم و گردش‌کار بدون کدنویسی (BPMN).'],
                    ['کارتابل یکپارچه', 'همه‌ی وظایف کاربر در یک کارتابل، روی وب و موبایل.'],
                ],
            ],
            [
                'pm-raga', 'مدیریت پروژه راگا', 'تحویل به‌موقع پروژه‌ها با شفافیت کامل', 'بهره‌وری', '📁',
                'ابزار مدیریت پروژه راگا برای تیم‌هایی که می‌خواهند زمان‌بندی، منابع و بودجه پروژه‌ها را دقیق کنترل کنند.',
                [
                    ['برنامه‌ریزی و گانت', 'ساختار شکست کار، وابستگی فعالیت‌ها و مسیر بحرانی.'],
                    ['مدیریت منابع', 'تخصیص نیرو، پایش ظرفیت و ثبت تایم‌شیت.'],
                    ['کنترل هزینه', 'بودجه در برابر واقعی و شاخص‌های ارزش کسب‌شده (EVM).'],
                ],
            ],
            [
                'bi-raga', 'داشبورد مدیریتی و BI راگا', 'تصمیم‌گیری مبتنی بر داده', 'مدیریت سازمان', '📈',
                'راگا BI داده‌های پراکنده سازمان را یکپارچه و به داشبوردهای مدیریتی زنده و قابل‌کاوش تبدیل می‌کند.',
                [
                    ['اتصال به منابع داده', 'اتصال به ERP، CRM، پایگاه‌های داده و فایل‌های اکسل.'],
                    ['داشبورد و KPI', 'شاخص‌های کلیدی هر واحد در یک نگاه، با هشدار خودکار.'],
                    ['تحلیل خودسرویس', 'کاربر کسب‌وکار بدون نیاز به IT گزارش می‌سازد.'],
                ],
            ],
            [
                'custom-raga', 'راهکارهای سفارشی', 'نرم‌افزار دقیقاً مطابق فرایند شما', 'بهره‌وری', '⚙️',
                'وقتی نرم‌افزار آماده پاسخ‌گوی نیاز خاص سازمان شما نیست، تیم راگا راهکار اختصاصی را طراحی، توسعه و پشتیبانی می‌کند.',
                [
                    ['تحلیل و طراحی', 'کشف نیازمندی‌ها، طراحی معماری و نمونه اولیه.'],
                    ['توسعه چابک', 'تحویل تدریجی در اسپرینت‌های کوتاه با بازخورد مستمر.'],
                    ['یکپارچه‌سازی', 'اتصال به سامانه‌های موجود از طریق API و وب‌سرویس.'],
                ],
            ],
        ];

        $products = collect();
        foreach ($data as $i => [$slug, $title, $tagline, $catName, $icon, $summary, $features]) {
            $product = Product::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'tagline' => $tagline,
                'category_id' => $cats[$catName]->id,
                'icon' => $icon,
                'summary' => $summary,
                'body' => "<p>{$summary}</p><h2>مناسب چه سازمان‌هایی است؟</h2><p>{$title} برای سازمان‌های متوسط و بزرگ ایرانی طراحی شده که به دنبال یکپارچگی داده، کاهش دوباره‌کاری و گزارش‌گیری قابل‌اتکا هستند.</p><h2>پیاده‌سازی</h2><p>تیم استقرار راگا با متدولوژی مشخص، انتقال داده، آموزش کاربران و پشتیبانی پس از راه‌اندازی را بر عهده می‌گیرد.</p>",
                'hero_media_id' => SeedMedia::make("product-{$slug}", $title, 'mockup')->id,
                'featured' => $i < 4,
                'cta_label' => 'درخواست دموی ' . $title,
                'status' => 'published',
                'published_at' => now()->subDays(30 - $i),
                'sort_order' => $i,
                'created_by' => $author?->id,
            ]);

            $product->features()->delete();
            foreach ($features as $fi => [$ft, $fd]) {
                $product->features()->create(['title' => $ft, 'description' => $fd, 'sort_order' => $fi, 'is_active' => true]);
            }

            $product->seo()->updateOrCreate([], [
                'meta_title' => "{$title} | {$tagline}",
                'meta_description' => Str::limit($summary, 155),
                'focus_keyword' => $title,
            ]);

            $products->push($product);
        }

        return $products;
    }

    private function services()
    {
        $data = [
            ['consulting', 'مشاوره تحول دیجیتال', '🧭', 'بررسی وضعیت موجود، تدوین نقشه راه فناوری و انتخاب راهکار مناسب سازمان شما.'],
            ['implementation', 'استقرار و پیاده‌سازی', '🚀', 'اجرای پروژه استقرار نرم‌افزار با متدولوژی مشخص، انتقال داده و آموزش کاربران.'],
            ['custom-dev', 'توسعه نرم‌افزار اختصاصی', '💻', 'طراحی و ساخت نرم‌افزار متناسب با فرایندهای خاص کسب‌وکار شما.'],
            ['integration', 'یکپارچه‌سازی سامانه‌ها', '🔗', 'اتصال سامانه‌های جزیره‌ای از طریق API، وب‌سرویس و گذرگاه سرویس سازمانی.'],
            ['support', 'پشتیبانی و نگهداری', '🛠️', 'پشتیبانی سطح‌بندی‌شده با SLA، به‌روزرسانی و بهینه‌سازی مستمر سامانه‌ها.'],
        ];

        $services = collect();
        foreach ($data as $i => [$slug, $title, $icon, $summary]) {
            $s = Service::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'icon' => $icon,
                'summary' => $summary,
                'body' => "<p>{$summary}</p><p>تیم راگا با تجربه اجرای ده‌ها پروژه سازمانی، این خدمت را با تعهد به زمان‌بندی و کیفیت ارائه می‌کند.</p>",
                'media_id' => SeedMedia::make("service-{$slug}", $title)->id,
                'featured' => $i < 3,
                'status' => 'published',
                'published_at' => now()->subDays(20 - $i),
                'sort_order' => $i,
            ]);
            $s->features()->where('kind', 'feature')->delete();
            foreach (['تیم متخصص و باتجربه', 'متدولوژی شفاف و گام‌به‌گام', 'تحویل و پشتیبانی تضمین‌شده'] as $fi => $ft) {
                $s->features()->create(['kind' => 'feature', 'title' => $ft, 'sort_order' => $fi, 'is_active' => true]);
            }
            $s->seo()->updateOrCreate([], ['meta_title' => "{$title} | راگا سافت‌ور", 'meta_description' => Str::limit($summary, 155)]);
            $services->push($s);
        }

        return $services;
    }

    private function customers()
    {
        $names = [
            'گروه صنعتی آریا', 'داروسازی رازی‌مهر', 'بانک توسعه پارس', 'پتروشیمی خلیج',
            'فروشگاه‌های زنجیره‌ای هفت', 'شرکت حمل‌ونقل رهپویان', 'هلدینگ ساختمانی مانا', 'بیمه آینده',
        ];
        $customers = collect();
        foreach ($names as $i => $name) {
            $customers->push(Customer::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'industry' => ['صنعت', 'دارو', 'بانکداری', 'پتروشیمی', 'خرده‌فروشی', 'لجستیک', 'ساختمان', 'بیمه'][$i],
                'logo_media_id' => SeedMedia::make("cust-{$i}", $name, 'logo')->id,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => $i,
            ]));
        }

        return $customers;
    }

    private function testimonials($customers): void
    {
        $data = [
            ['مدیر مالی', 'گروه صنعتی آریا', 'با استقرار راگا ERP زمان بستن ماهانه حساب‌ها از دو هفته به سه روز رسید و گزارش‌ها قابل‌اتکا شد.'],
            ['معاون فروش', 'فروشگاه‌های زنجیره‌ای هفت', 'دید یکپارچه به مشتریان و اتوماسیون پیگیری‌ها، نرخ تبدیل سرنخ ما را حدود ۳۰٪ افزایش داد.'],
            ['مدیر فناوری اطلاعات', 'هلدینگ ساختمانی مانا', 'تیم راگا در پیاده‌سازی و یکپارچه‌سازی با سامانه‌های قبلی ما واقعاً حرفه‌ای عمل کرد.'],
        ];
        foreach ($data as $i => [$role, $company, $body]) {
            Testimonial::updateOrCreate(
                ['author_name' => $role . ' ' . $company],
                [
                    'author_title' => $role . ' | ' . $company,
                    'customer_id' => $customers->firstWhere('name', $company)?->id,
                    'body' => $body,
                    'rating' => 5,
                    'is_featured' => true,
                    'is_active' => true,
                    'sort_order' => $i,
                ],
            );
        }
    }

    private function team(): void
    {
        $data = [
            ['علی رضایی', 'بنیان‌گذار و مدیرعامل'],
            ['مریم کاظمی', 'مدیر محصول'],
            ['حسین مرادی', 'مدیر فنی'],
            ['سارا احمدی', 'مدیر استقرار و موفقیت مشتری'],
        ];
        foreach ($data as $i => [$name, $role]) {
            TeamMember::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'role_title' => $role,
                'bio' => "{$name} با بیش از یک دهه تجربه در حوزه نرم‌افزار سازمانی، در راگا مسئولیت {$role} را بر عهده دارد.",
                'photo_media_id' => SeedMedia::make("team-{$i}", $name, 'square')->id,
                'sort_order' => $i, 'is_active' => true,
            ]);
        }
    }

    private function blog(): array
    {
        $author = User::first();
        $cats = collect([
            'تحول دیجیتال' => 'digital-transformation',
            'ERP و مالی' => 'erp',
            'داده و BI' => 'data-bi',
        ])->map(fn ($slug, $name) => BlogCategory::updateOrCreate(['slug' => $slug], ['name' => $name, 'sort_order' => 0]));

        $tags = collect(['ERP', 'CRM', 'هوش تجاری', 'اتوماسیون', 'یکپارچه‌سازی', 'امنیت داده'])
            ->mapWithKeys(fn ($t) => [$t => BlogTag::updateOrCreate(['slug' => Str::slug($t)], ['name' => $t])]);

        $posts = [
            ['digital-transformation-roadmap', 'نقشه راه تحول دیجیتال برای سازمان‌های ایرانی', 'تحول دیجیتال', ['تحول دیجیتال' => null],
                'تحول دیجیتال یک پروژه نرم‌افزاری نیست؛ تغییری در نحوه‌ی کارکردن سازمان است. در این مقاله یک نقشه راه عملی در پنج گام ارائه می‌کنیم.', ['ERP', 'اتوماسیون']],
            ['choosing-erp', 'چطور ERP مناسب سازمان‌مان را انتخاب کنیم؟', 'ERP و مالی', [],
                'انتخاب اشتباه ERP هزینه‌ی سنگینی دارد. معیارهای کلیدی ارزیابی، از تطبیق فرایندی تا توان تیم پیاده‌ساز را بررسی می‌کنیم.', ['ERP']],
            ['bi-for-managers', 'هوش تجاری برای مدیران: از گزارش به بینش', 'داده و BI', [],
                'داشبورد خوب فقط عدد نشان نمی‌دهد؛ به سؤال بعدی مدیر پاسخ می‌دهد. اصول طراحی داشبورد مدیریتی مؤثر.', ['هوش تجاری']],
            ['data-security', 'امنیت داده در نرم‌افزارهای سازمانی؛ چه باید بدانیم', 'تحول دیجیتال', [],
                'از کنترل دسترسی نقش‌محور تا رمزنگاری و پشتیبان‌گیری؛ حداقل‌های امنیتی که هر سامانه سازمانی باید داشته باشد.', ['امنیت داده']],
            ['office-automation-benefits', 'اتوماسیون اداری واقعاً چه چیزی را حل می‌کند؟', 'تحول دیجیتال', [],
                'فراتر از «حذف کاغذ»: شفافیت فرایند، پاسخ‌گویی و داده‌ی قابل تحلیل، سه دستاورد اصلی اتوماسیون اداری.', ['اتوماسیون']],
            ['system-integration', 'یکپارچه‌سازی سامانه‌ها؛ پایان جزیره‌های اطلاعاتی', 'داده و BI', [],
                'وقتی هر واحد داده‌ی خودش را دارد، تصمیم سازمانی سخت می‌شود. الگوهای رایج یکپارچه‌سازی و انتخاب درست.', ['یکپارچه‌سازی', 'هوش تجاری']],
        ];

        $created = collect();
        foreach ($posts as $i => [$slug, $title, $catName, $_, $excerpt, $postTags]) {
            $post = BlogPost::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'category_id' => $cats[$catName]->id,
                'author_id' => $author?->id,
                'excerpt' => $excerpt,
                'body' => "<p>{$excerpt}</p><h2>مقدمه</h2><p>سازمان‌های ایرانی در مسیر دیجیتالی‌شدن با چالش‌های مشابهی روبه‌رو هستند: داده‌ی پراکنده، فرایندهای غیرشفاف و مقاومت در برابر تغییر.</p><h2>گام‌های عملی</h2><ul><li>ارزیابی بلوغ دیجیتال</li><li>اولویت‌بندی فرایندها بر اساس ارزش</li><li>انتخاب راهکار و تیم پیاده‌ساز</li><li>اجرای تدریجی و اندازه‌گیری نتایج</li></ul><h2>جمع‌بندی</h2><p>موفقیت در گروِ تعهد مدیریت ارشد و تمرکز بر نتایج کسب‌وکاری است، نه صرفاً استقرار ابزار.</p>",
                'cover_media_id' => SeedMedia::make("post-{$slug}", $title, 'cover')->id,
                'is_featured' => $i === 0,
                'status' => 'published',
                'published_at' => now()->subDays(3 * ($i + 1)),
            ]);
            $post->tags()->sync(collect($postTags)->map(fn ($t) => $tags[$t]->id)->all());
            $post->seo()->updateOrCreate([], ['meta_title' => "{$title} | وبلاگ راگا", 'meta_description' => Str::limit($excerpt, 155)]);
            $created->push($post);
        }

        return [$cats, $created];
    }

    private function projects(): void
    {
        $data = [
            ['erp-aria', 'استقرار ERP در گروه صنعتی آریا', 'گروه صنعتی آریا', 'یکپارچه‌سازی مالی، انبار و تولید در ۴ کارخانه و کاهش زمان بستن حساب‌های ماهانه به یک‌سوم.'],
            ['crm-haft', 'راه‌اندازی CRM و باشگاه مشتریان فروشگاه‌های هفت', 'فروشگاه‌های زنجیره‌ای هفت', 'پیاده‌سازی CRM برای ۱۲۰ شعبه با باشگاه مشتریان و کمپین‌های هدفمند.'],
            ['bi-pars', 'داشبورد مدیریتی بانک توسعه پارس', 'بانک توسعه پارس', 'ساخت داشبورد ریسک و عملکرد شعب با اتصال به Core Banking و انبار داده.'],
            ['automation-mana', 'اتوماسیون اداری هلدینگ مانا', 'هلدینگ ساختمانی مانا', 'حذف مکاتبات کاغذی و پیاده‌سازی ۳۰ فرایند سازمانی روی فرایندساز راگا.'],
        ];
        foreach ($data as $i => [$slug, $title, $client, $summary]) {
            $p = Project::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'client_name' => $client,
                'customer_id' => Customer::where('name', $client)->value('id'),
                'summary' => $summary,
                'body' => "<p>{$summary}</p><h2>چالش</h2><p>سازمان با سامانه‌های جزیره‌ای و گزارش‌گیری دستی مواجه بود.</p><h2>راهکار راگا</h2><p>پیاده‌سازی گام‌به‌گام، انتقال داده و آموزش کاربران کلیدی.</p><h2>نتیجه</h2><p>شفافیت داده، کاهش دوباره‌کاری و تصمیم‌گیری سریع‌تر مدیران.</p>",
                'cover_media_id' => SeedMedia::make("project-{$slug}", $title, 'cover')->id,
                'completed_on' => now()->subMonths(6 - $i)->startOfMonth(),
                'featured' => true,
                'status' => 'published',
                'published_at' => now()->subMonths(6 - $i),
                'sort_order' => $i,
            ]);
            $p->seo()->updateOrCreate([], ['meta_title' => "{$title} | نمونه‌کار راگا", 'meta_description' => Str::limit($summary, 155)]);
        }
    }

    private function faqs(): void
    {
        $general = FaqCategory::updateOrCreate(['slug' => 'general'], ['name' => 'عمومی', 'sort_order' => 0]);
        $impl = FaqCategory::updateOrCreate(['slug' => 'implementation'], ['name' => 'پیاده‌سازی و پشتیبانی', 'sort_order' => 1]);

        $items = [
            [$general, 'راگا سافت‌ور چه محصولاتی دارد؟', 'راگا مجموعه‌ای از راهکارهای سازمانی شامل ERP، CRM، اتوماسیون اداری، مدیریت پروژه و داشبورد مدیریتی و BI ارائه می‌کند و در کنار آن راهکارهای سفارشی نیز توسعه می‌دهد.'],
            [$general, 'آیا نرم‌افزارها روی زیرساخت داخلی سازمان نصب می‌شوند؟', 'بله. محصولات راگا هم به‌صورت نصب روی سرور سازمان (On-Premise) و هم ابری قابل ارائه هستند.'],
            [$general, 'آیا امکان دموی محصول وجود دارد؟', 'بله، پس از ثبت درخواست از طریق فرم تماس، کارشناسان ما یک جلسه دموی اختصاصی متناسب با نیاز سازمان شما برگزار می‌کنند.'],
            [$general, 'محصولات با استانداردهای حسابداری ایران سازگارند؟', 'بله، ماژول مالی راگا مطابق استانداردهای حسابداری ایران و الزامات سازمان امور مالیاتی طراحی شده است.'],
            [$impl, 'یک پروژه استقرار معمولاً چقدر طول می‌کشد؟', 'بسته به دامنه، بین ۲ تا ۶ ماه. پیاده‌سازی به‌صورت فازبندی‌شده انجام می‌شود تا سازمان زودتر به نتیجه برسد.'],
            [$impl, 'انتقال داده از سیستم قبلی چگونه انجام می‌شود؟', 'تیم استقرار راگا ابزارها و فرایند مشخصی برای پاک‌سازی، نگاشت و انتقال داده از سامانه‌های پیشین دارد.'],
            [$impl, 'آموزش کاربران چگونه است؟', 'آموزش حضوری و آنلاین برای کاربران کلیدی و کاربران نهایی، به‌همراه مستندات و ویدیوهای آموزشی ارائه می‌شود.'],
            [$impl, 'پشتیبانی پس از راه‌اندازی چه پوششی دارد؟', 'پشتیبانی سطح‌بندی‌شده با توافق‌نامه سطح خدمت (SLA)، شامل رفع اشکال، پاسخ‌گویی کارشناسی و به‌روزرسانی نسخه.'],
            [$impl, 'آیا امکان یکپارچه‌سازی با نرم‌افزارهای فعلی ما هست؟', 'بله. راگا از طریق API و وب‌سرویس با سامانه‌های موجود مانند Core Banking، سامانه‌های تولید و درگاه‌های پرداخت یکپارچه می‌شود.'],
            [$general, 'هزینه محصولات چگونه محاسبه می‌شود؟', 'قیمت‌گذاری بر اساس ماژول‌های موردنیاز، تعداد کاربر و دامنه پیاده‌سازی تعیین می‌شود. برای دریافت پیشنهاد قیمت با ما تماس بگیرید.'],
        ];
        foreach ($items as $i => [$cat, $q, $a]) {
            Faq::updateOrCreate(['question' => $q], [
                'category_id' => $cat->id, 'answer' => "<p>{$a}</p>", 'sort_order' => $i, 'is_active' => true,
            ]);
        }
    }

    private function menus($products): void
    {
        $header = NavigationMenu::updateOrCreate(['location' => 'header'], ['name' => 'منوی اصلی']);
        $header->items()->delete();
        // Homepage is a single-page experience: nav scrolls to sections.
        // Real standalone pages (blog, contact) keep full links.
        $items = [
            ['خانه', 'url', '/'],
            ['درباره ما', 'url', '/#about'],
            ['خدمات', 'url', '/#services'],
            ['محصولات', 'url', '/#products'],
            ['پروژه‌ها', 'url', '/#projects'],
            ['وبلاگ', 'url', '/blog'],
            ['تماس با ما', 'url', '/contact'],
        ];
        foreach ($items as $i => [$label, $type, $val]) {
            $header->items()->create(['label' => $label, 'link_type' => $type, 'link_value' => $val, 'sort_order' => $i, 'is_active' => true]);
        }

        $f1 = NavigationMenu::updateOrCreate(['location' => 'footer_1'], ['name' => 'محصولات']);
        $f1->items()->delete();
        foreach ($products->take(5) as $i => $p) {
            $f1->items()->create(['label' => $p->title, 'link_type' => 'product', 'link_value' => $p->slug, 'sort_order' => $i, 'is_active' => true]);
        }

        $f2 = NavigationMenu::updateOrCreate(['location' => 'footer_2'], ['name' => 'شرکت']);
        $f2->items()->delete();
        foreach ([['درباره ما', '/about'], ['وبلاگ', '/blog'], ['نمونه‌کارها', '/projects'], ['تماس با ما', '/contact']] as $i => [$l, $u]) {
            $f2->items()->create(['label' => $l, 'link_type' => 'url', 'link_value' => $u, 'sort_order' => $i, 'is_active' => true]);
        }

        $f3 = NavigationMenu::updateOrCreate(['location' => 'footer_3'], ['name' => 'منابع']);
        $f3->items()->delete();
        foreach ([['خدمات', '/services'], ['سؤالات متداول', '/faq']] as $i => [$l, $u]) {
            $f3->items()->create(['label' => $l, 'link_type' => 'url', 'link_value' => $u, 'sort_order' => $i, 'is_active' => true]);
        }
    }

    private function pages(): void
    {
        $about = Page::updateOrCreate(['slug' => 'about'], [
            'title' => 'درباره راگا سافت‌ور',
            'template' => 'default',
            'excerpt' => 'راگا سافت‌ور، توسعه‌دهنده راهکارهای نرم‌افزاری سازمانی برای کسب‌وکارهای ایرانی.',
            'status' => 'published',
            'published_at' => now(),
            'show_in_sitemap' => true,
        ]);
        $about->sections()->delete();
        $about->sections()->createMany([
            ['type' => 'rich_text', 'name' => 'معرفی', 'sort_order' => 0, 'is_active' => true, 'settings' => [
                'heading' => 'ما چه کسانی هستیم',
                'body' => '<p>راگا سافت‌ور از سال ۱۳۹۲ با هدف ساخت نرم‌افزارهای سازمانی بومی، یکپارچه و قابل‌اتکا فعالیت می‌کند. تمرکز ما بر حل مسئله‌ی واقعی کسب‌وکار است، نه صرفاً تحویل نرم‌افزار.</p><p>تا امروز بیش از ۱۲۰ سازمان ایرانی در حوزه‌های صنعت، بانکداری، دارو، خرده‌فروشی و خدمات، از راهکارهای راگا استفاده می‌کنند.</p>',
                'width' => 'normal',
            ]],
            ['type' => 'stats', 'name' => 'آمار', 'sort_order' => 1, 'is_active' => true, 'settings' => [
                'heading' => 'راگا در یک نگاه',
                'items' => [
                    ['value' => '+۱۲۰', 'label' => 'سازمان'],
                    ['value' => '+۱۲', 'label' => 'سال تجربه'],
                    ['value' => '+۶۰', 'label' => 'متخصص'],
                    ['value' => '۹۸٪', 'label' => 'رضایت پشتیبانی'],
                ],
            ]],
            ['type' => 'advantages', 'name' => 'ارزش‌ها', 'sort_order' => 2, 'is_active' => true, 'settings' => [
                'heading' => 'چه چیزی ما را متمایز می‌کند',
                'items' => [
                    ['icon' => '🎯', 'title' => 'تمرکز بر نتیجه کسب‌وکار', 'description' => 'موفقیت پروژه را با شاخص‌های کسب‌وکاری می‌سنجیم، نه فقط تحویل فنی.'],
                    ['icon' => '🇮🇷', 'title' => 'بومی و منطبق بر مقررات ایران', 'description' => 'سازگار با استانداردهای حسابداری، مالیات و بیمه.'],
                    ['icon' => '🤝', 'title' => 'همراهی بلندمدت', 'description' => 'از مشاوره تا پشتیبانی، در تمام مسیر کنار شما هستیم.'],
                ],
            ]],
            ['type' => 'team_grid', 'name' => 'تیم', 'sort_order' => 3, 'is_active' => true, 'settings' => ['heading' => 'تیم رهبری']],
            ['type' => 'cta_banner', 'name' => 'CTA', 'sort_order' => 4, 'is_active' => true, 'settings' => [
                'heading' => 'بیایید درباره سازمان شما صحبت کنیم', 'cta_label' => 'تماس با ما', 'cta_url' => '/contact', 'style' => 'gradient',
            ]],
        ]);
        $about->seo()->updateOrCreate([], ['meta_title' => 'درباره راگا سافت‌ور | شرکت نرم‌افزار سازمانی', 'meta_description' => 'با تیم، تجربه و ارزش‌های راگا سافت‌ور آشنا شوید؛ توسعه‌دهنده ERP، CRM و اتوماسیون اداری برای سازمان‌های ایرانی.']);

        $contact = Page::updateOrCreate(['slug' => 'contact'], [
            'title' => 'تماس با ما',
            'template' => 'default',
            'excerpt' => 'برای مشاوره، دمو یا همکاری با راگا سافت‌ور با ما در تماس باشید.',
            'status' => 'published',
            'published_at' => now(),
            'show_in_sitemap' => true,
        ]);
        $contact->seo()->updateOrCreate([], ['meta_title' => 'تماس با راگا سافت‌ور', 'meta_description' => 'راه‌های ارتباط با راگا سافت‌ور: تلفن، ایمیل، نشانی و فرم تماس.']);
    }

    private function homepage(): void
    {
        $home = Page::updateOrCreate(['is_homepage' => true], [
            'title' => 'صفحه اصلی',
            'slug' => 'home',
            'template' => 'landing',
            'status' => 'published',
            'published_at' => now(),
            'show_in_sitemap' => true,
        ]);

        // 3 logo cards for the hero — real logos from the (DB-managed) customers.
        $heroLogoIds = Customer::whereNotNull('logo_media_id')->orderBy('sort_order')->limit(3)->pluck('logo_media_id')->all();

        $home->sections()->delete();
        $home->sections()->createMany([
            ['type' => 'hero', 'name' => 'هیرو', 'sort_order' => 0, 'is_active' => true, 'settings' => [
                'eyebrow' => 'نرم‌افزار سازمانی راگا',
                'heading' => 'سازمان‌تان را یکپارچه و هوشمند مدیریت کنید',
                'subheading' => 'راگا سافت‌ور با مجموعه‌ای از راهکارهای بومی ERP، CRM، اتوماسیون اداری و هوش تجاری، داده‌های پراکنده سازمان شما را به تصمیم‌های سریع و مطمئن تبدیل می‌کند.',
                'primary_cta_label' => 'درخواست مشاوره رایگان',
                'primary_cta_url' => '#contact',
                'secondary_cta_label' => 'مشاهده محصولات',
                'secondary_cta_url' => '#products',
                'media_id' => SeedMedia::make('home-hero', 'داشبورد مدیریتی راگا', 'mockup')->id,
                'logo_cards' => $heroLogoIds,
                'stats' => [
                    ['value' => '+۱۲۰', 'label' => 'سازمان'],
                    ['value' => '+۱۲', 'label' => 'سال تجربه'],
                    ['value' => '۹۸٪', 'label' => 'رضایت مشتری'],
                ],
            ]],
            ['type' => 'about_intro', 'name' => 'معرفی کوتاه', 'sort_order' => 1, 'is_active' => true, 'settings' => [
                'heading' => 'همراه سازمان‌های ایرانی در مسیر تحول دیجیتال',
                'body' => '<p>بیش از یک دهه است که راگا سافت‌ور نرم‌افزارهای سازمانی می‌سازد؛ نرم‌افزارهایی که فرایند واقعی کسب‌وکار ایرانی را می‌فهمند و با مقررات آن سازگارند.</p><p>از یک استارتاپ رو به رشد تا هلدینگ‌های بزرگ، راهکار راگا با شما مقیاس می‌گیرد.</p>',
                'media_id' => SeedMedia::make('home-about', 'تیم راگا سافت‌ور')->id,
                'cta_label' => 'درباره ما',
                'cta_url' => '/about',
            ]],
            ['type' => 'services_grid', 'name' => 'خدمات', 'sort_order' => 2, 'is_active' => true, 'settings' => [
                'heading' => 'خدمات ما',
                'subheading' => 'از مشاوره و پیاده‌سازی تا توسعه اختصاصی و پشتیبانی؛ کنار سازمان شما در تمام مسیر.',
                'mode' => 'featured', 'limit' => 6,
            ]],
            ['type' => 'products_grid', 'name' => 'محصولات', 'sort_order' => 3, 'is_active' => true, 'settings' => [
                'heading' => 'محصولات و راهکارها',
                'subheading' => 'هر ماژول به‌تنهایی ارزش‌آفرین است و در کنار هم یک سامانه سازمانی یکپارچه می‌سازد.',
                'mode' => 'featured', 'limit' => 6, 'columns' => '3',
            ]],
            ['type' => 'advantages', 'name' => 'مزیت‌ها', 'sort_order' => 4, 'is_active' => true, 'settings' => [
                'heading' => 'چرا راگا سافت‌ور؟',
                'subheading' => 'انتخاب یک شریک فناوری، تصمیمی بلندمدت است.',
                'items' => [
                    ['icon' => '🔗', 'title' => 'یکپارچگی واقعی', 'description' => 'یک منبع واحد حقیقت برای کل سازمان؛ بدون دوباره‌کاری و مغایرت.'],
                    ['icon' => '🇮🇷', 'title' => 'بومی و منطبق بر مقررات', 'description' => 'سازگار با استانداردهای حسابداری، مالیات، بیمه و فرایندهای اداری ایران.'],
                    ['icon' => '📈', 'title' => 'تصمیم مبتنی بر داده', 'description' => 'داشبوردهای مدیریتی زنده که به سؤال بعدی مدیر پاسخ می‌دهند.'],
                    ['icon' => '🛡️', 'title' => 'امنیت و کنترل دسترسی', 'description' => 'دسترسی نقش‌محور، ثبت رویداد و پشتیبان‌گیری منظم.'],
                    ['icon' => '🤝', 'title' => 'پشتیبانی متعهد', 'description' => 'تیم پشتیبانی با SLA مشخص، در کنار شما پس از راه‌اندازی.'],
                    ['icon' => '⚙️', 'title' => 'انعطاف و توسعه‌پذیری', 'description' => 'فرایندساز و API باز برای تطبیق با نیاز خاص سازمان شما.'],
                ],
            ]],
            ['type' => 'stats', 'name' => 'آمار', 'sort_order' => 5, 'is_active' => true, 'settings' => [
                'heading' => 'راگا در یک نگاه',
                'items' => [
                    ['value' => '+۱۲۰', 'label' => 'سازمان'],
                    ['value' => '+۱۲', 'label' => 'سال تجربه'],
                    ['value' => '+۶۰', 'label' => 'متخصص'],
                    ['value' => '۹۸٪', 'label' => 'رضایت پشتیبانی'],
                ],
            ]],
            ['type' => 'projects_grid', 'name' => 'نمونه‌کارها', 'sort_order' => 6, 'is_active' => true, 'settings' => [
                'heading' => 'پروژه‌هایی که به نتیجه رسیدند', 'limit' => 3,
            ]],
            ['type' => 'customers_logos', 'name' => 'مشتریان', 'sort_order' => 7, 'is_active' => true, 'settings' => [
                'heading' => 'مورد اعتماد سازمان‌های پیشرو', 'grayscale' => true,
            ]],
            ['type' => 'testimonials', 'name' => 'نظرات مشتریان', 'sort_order' => 8, 'is_active' => true, 'settings' => [
                'heading' => 'مشتریان ما چه می‌گویند', 'limit' => 3,
            ]],
            ['type' => 'blog_latest', 'name' => 'مقالات منتخب', 'sort_order' => 9, 'is_active' => true, 'settings' => [
                'heading' => 'مقالات منتخب', 'subheading' => 'تجربه‌ها و تحلیل‌های ما درباره نرم‌افزار سازمانی.', 'limit' => 3,
            ]],
            ['type' => 'faq', 'name' => 'سؤالات متداول', 'sort_order' => 10, 'is_active' => true, 'settings' => [
                'heading' => 'سؤالات متداول', 'limit' => 6,
            ]],
            ['type' => 'cta_banner', 'name' => 'CTA نهایی', 'sort_order' => 11, 'is_active' => true, 'settings' => [
                'heading' => 'آماده‌اید کسب‌وکار خود را هوشمندتر مدیریت کنید؟',
                'subheading' => 'یک جلسه‌ی مشاوره‌ی رایگان با کارشناسان راگا رزرو کنید.',
                'cta_label' => 'درخواست مشاوره', 'cta_url' => route('contact'), 'style' => 'gradient',
            ]],
        ]);

        $home->seo()->updateOrCreate([], [
            'meta_title' => 'راگا سافت‌ور | نرم‌افزار سازمانی، ERP، CRM و هوش تجاری',
            'meta_description' => 'راگا سافت‌ور؛ توسعه‌دهنده راهکارهای یکپارچه سازمانی شامل ERP، CRM، اتوماسیون اداری، مدیریت پروژه و داشبورد مدیریتی و BI برای کسب‌وکارهای ایرانی.',
        ]);
    }
}
