# RagaSoftware — وب‌سایت رسمی (`ragasoftware_site`)

وب‌سایت رسمی و سامانه مدیریت محتوای شرکت **راگا سافت‌ور**.
پروژه‌ای کاملاً مستقل، دیتابیس‌محور و آماده استقرار روی هاست‌های معمولی PHP/MySQL.

| بخش | تکنولوژی |
|---|---|
| بک‌اند | Laravel 13 · PHP ۸.۳+ |
| سایت عمومی | **Blade + Tailwind CSS v4 + Alpine.js** (رندر سمت سرور، SEO کامل، بدون SPA) |
| پنل مدیریت | **Inertia 2 + React 19 + TypeScript + Ant Design** (زیر مسیر `/admin`) |
| پایگاه‌داده | **MySQL 8** — در توسعه: Docker Compose |
| بیلد | Vite |
| RBAC | `spatie/laravel-permission` |
| رسانه | `intervention/image` (تولید نسخه‌های Responsive + WebP) |

> **مهم‌ترین قانون پروژه:** هیچ محتوای قابل‌مشاهده‌ی سایت در کد ثابت (Hard-Code) نیست.
> عنوان‌ها، متن‌ها، تصاویر، محصولات، خدمات، مقالات، منوها، فوتر، CTAها، SEO و **ترتیب بخش‌های صفحات**
> همگی از MySQL خوانده و از پنل مدیریت ویرایش می‌شوند. تنها ساختار Component و Layout در کد است.

---

## ۱) پیش‌نیازها

- PHP ۸.۳ به بالا با اکستنشن‌های `pdo_mysql`, `mbstring`, `gd`, `intl`, `fileinfo`, `openssl`, `curl`, `zip`
- Composer 2
- Node.js ۲۰ به بالا + npm
- برای توسعه‌ی محلی: Docker Desktop (برای بالا آوردن MySQL)

> در ویندوز، اگر `php` روی PATH نیست، همه‌ی دستورهای `php` را با مسیر کامل اجرا کنید
> (مثلاً `D:\Project\PHP\php.exe artisan ...`) و برای Composer از `php composer.phar` استفاده کنید.

---

## ۲) راه‌اندازی محیط توسعه

### گام‌به‌گام (توصیه‌شده: MySQL در Docker، بقیه روی سیستم)

```bash
git clone <repo> ragasoftware_site
cd ragasoftware_site

cp .env.example .env
# در .env مقادیر زیر را تنظیم کنید:
#   DB_DATABASE / DB_USERNAME / DB_PASSWORD / DB_ROOT_PASSWORD
#   SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD   ← حساب اولیه مدیر
#   DB_FORWARD_PORT (پیش‌فرض 3306) و APP_FORWARD_PORT (پیش‌فرض 8085)

composer install
npm install

# فقط دیتابیس را بالا بیاورید
docker compose up -d mysql

php artisan key:generate
php artisan migrate --seed          # ساخت جداول + محتوای اولیه فارسی

# دو ترمینال:
npm run dev                         # سرور توسعه Vite (HMR)
php artisan serve                   # http://127.0.0.1:8000
```

- سایت:  `http://127.0.0.1:8000`
- پنل مدیریت:  `http://127.0.0.1:8000/admin`  ← با `SEED_ADMIN_EMAIL` / `SEED_ADMIN_PASSWORD`

### اجرای کامل با Docker (app + nginx + mysql + vite + adminer)

```bash
cp .env.example .env
docker compose up -d --build

php artisan key:generate            # یک بار، یا: docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

- سایت / پنل:  `http://localhost:8085` و `http://localhost:8085/admin`
- Adminer (مدیریت DB):  `http://localhost:8081`  (سرور: `mysql`)
- Vite:  پورت `5173`

> کانتینر `app` به‌صورت خودکار `DB_HOST=mysql` را جایگزین می‌کند؛ نیازی به تغییر `.env` نیست.

---

## ۳) دیتابیس

- موتور: MySQL 8 (InnoDB, `utf8mb4_unicode_ci`)
- بدون Stored Procedure / Trigger / قابلیت خاص MySQL → **کاملاً قابل‌انتقال** به هاست اشتراکی.
- همه‌ی تغییرات ساختار از طریق **Migration**های Laravel است (`database/migrations/`).
- محتوای اولیه: `php artisan db:seed` (Seeder اصلی: `RealisticContentSeeder`).

### چندزبانه بودن (آماده برای آینده)

جدول‌های محتوا ستون `locale` (پیش‌فرض `fa`) و `translation_group` دارند و کوئری‌ها بر اساس
`app()->getLocale()` فیلتر می‌شوند. افزودن زبان انگلیسی در آینده = افزودن ردیف، **بدون تغییر ساختار**.
نقطه‌ی توسعه: `App\Http\Middleware\SetPublicLocale` و `config/cms.php → locales`.

---

## ۴) بیلد asset‌ها

```bash
npm run build            # خروجی در public/build/  (کامیت می‌شود)
npm run types            # بررسی TypeScript
```

`public/build/` **عمداً در گیت کامیت می‌شود** تا نیازی به Node روی سرور نباشد.
پس از هر تغییر در `resources/js` یا `resources/css`، `npm run build` را اجرا و خروجی را همراه سورس کامیت کنید.

> `vendor/` در گیت **نیست**؛ روی سرور با `composer install --no-dev` نصب می‌شود.

---

## ۵) استقرار روی هاست اشتراکی PHP + MySQL

### خودکار (cPanel Git)
فایل `.cpanel.yml` در ریشه‌ی پروژه هست. در cPanel → Git Version Control پس از
«Update from Remote»، دکمه‌ی **Deploy HEAD Commit** را بزنید. این تسک‌ها اجرا می‌شوند:
`public/build` کهنه پاک می‌شود → کل ریپو کپی می‌شود (بدون `vendor/`) →
`composer install --no-dev --optimize-autoloader` → بازسازی کش‌ها.
Document Root دامنه باید روی `public_html/public` باشد.
پیش‌نیاز: در محیط دیپلوی هاست `composer` و `php` در دسترس باشند
(اگر نبود، مسیر آن‌ها را در `.cpanel.yml` تنظیم کنید یا یک‌بار دستی `composer install` بزنید).

### دستی

```bash
# روی سرور:
git clone <repo> && cd ragasoftware_site      # یا git pull برای به‌روزرسانی
composer install --no-dev --optimize-autoloader

cp .env.example .env
# تنظیم: APP_ENV=production ، APP_DEBUG=false ، APP_URL=https://ragasoftware.ir
#        DB_HOST / DB_DATABASE / DB_USERNAME / DB_PASSWORD  ← اطلاعات MySQL هاست
php artisan key:generate

php artisan migrate --force
php artisan db:seed --force                    # فقط در اولین استقرار

php artisan storage:link                        # اختیاری؛ رسانه‌ها در public/uploads هستند و نیازی نیست
php artisan config:cache route:cache view:cache event:cache
```

**تنظیمات مورد نیاز هاست:**

1. **Document Root** روی پوشه‌ی `public/` تنظیم شود.
   اگر امکان تغییر Document Root نیست، محتوای `public/` را به ریشه منتقل و مسیرها را در `index.php` اصلاح کنید.
2. **Cron** (برای انتشار زمان‌بندی‌شده و بازسازی نقشه سایت) — یک خط:
   ```
   * * * * * cd /path/to/ragasoftware_site && php artisan schedule:run >> /dev/null 2>&1
   ```
3. پوشه‌های `storage/` و `bootstrap/cache/` قابل نوشتن باشند.
4. صف (`QUEUE_CONNECTION=database`) و کش (`CACHE_STORE=database`) روی دیتابیس‌اند؛ **نیازی به Redis نیست**.
   کارهای صف توسط `schedule:run` یا به‌صورت `sync` پردازش می‌شوند.

**پس از هر به‌روزرسانی:**
```bash
git pull
composer install --no-dev -o
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache route:cache view:cache
```

### آپلود رسانه‌ها

فایل‌ها مستقیماً در `public/uploads/` ذخیره می‌شوند (نیازی به symlink `storage:link` نیست).
مطمئن شوید این پوشه روی سرور قابل نوشتن است و در بک‌آپ‌ها لحاظ می‌شود.
محدودیت حجم/نوع در `config/cms.php → media` و `MEDIA_MAX_UPLOAD_KB` در `.env`.

---

## ۶) پشتیبان‌گیری و بازیابی

### دیتابیس

**گرفتن بک‌آپ (از کانتینر Docker):**
```bash
docker compose exec mysql sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' > backup_$(date +%F).sql
```

**گرفتن بک‌آپ (هاست اشتراکی / MySQL محلی):**
```bash
mysqldump -h HOST -u USER -p DBNAME > backup.sql
```

**بازیابی:**
```bash
# Docker
docker compose exec -T mysql sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < backup.sql
# هاست
mysql -h HOST -u USER -p DBNAME < backup.sql
```

خروجی `mysqldump` مستقیماً در phpMyAdmin/cPanel هاست قابل import است.

### فایل‌ها

از پوشه‌ی `public/uploads/` (به‌جز `seed/`) به‌صورت دوره‌ای بک‌آپ بگیرید و همراه فایل `.sql` نگه دارید.
بازیابی کامل = بازگرداندن دیتابیس + کپی `public/uploads/`.

---

## ۷) راهنمای پنل مدیریت

مسیر: `/admin` — گروه منوی **«مدیریت وب‌سایت»**. دسترسی‌ها بر اساس نقش (spatie) کنترل می‌شوند.
نقش‌های پیش‌فرض: `super-admin` (کامل)، `admin`، `editor` (فقط محتوا)، `author` (فقط وبلاگ).

| بخش | کارکرد |
|---|---|
| **تنظیمات سایت** | نام شرکت، لوگو، اطلاعات تماس، فوتر، اسکریپت‌های GA4/GTM/Search Console، پیش‌فرض‌های سئو |
| **صفحه اصلی** | همان صفحه‌ی `is_homepage`؛ ویرایش در Section Builder |
| **صفحات** | صفحات ثابت و پویا؛ هر صفحه با **Section Builder** (Drag & Drop) از ۱۸ نوع بخش ساخته می‌شود؛ فعال/غیرفعال، ترتیب، محتوا، تصویر، CTA و تب سئو |
| **محصولات / خدمات / نمونه‌کارها** | CRUD کامل + قابلیت‌ها + رسانه + تب سئو + وضعیت انتشار (پیش‌نویس/زمان‌بندی/منتشر) |
| **مشتریان / نظرات / اعضای تیم** | مدیریت لیستی سریع |
| **مقالات** | ویرایشگر متن (TipTap)، دسته، برچسب، نویسنده، تصویر شاخص، سئو، زمان‌بندی انتشار |
| **سؤالات متداول** | پرسش/پاسخ + دسته‌بندی (خروجی با FAQ Schema) |
| **منوها** | منوی هدر، سه ستون فوتر، منوی موبایل، لینک‌های حقوقی — کاملاً پویا |
| **رسانه‌ها** | آپلود، پوشه‌بندی، Alt/عنوان؛ تولید خودکار نسخه‌های Responsive + WebP |
| **SEO و ریدایرکت** | ریدایرکت ۳۰۱/۳۰۲، وضعیت نقشه سایت، لینک robots.txt |
| **پیام‌های تماس** | صندوق ورودی فرم تماس با وضعیت (جدید/خوانده/پاسخ‌داده/بسته) |
| **کاربران و نقش‌ها** | مدیریت کاربران و ماتریس دسترسی نقش‌ها |

### تغییر ترتیب بخش‌های صفحه
در ویرایش صفحه → تب «بخش‌های صفحه» → آیکن جابه‌جایی هر بخش را بکشید و رها کنید → «ذخیره».
ترتیب در ستون `sort_order` جدول `page_sections` نگهداری و در سایت اعمال می‌شود.

### وضعیت انتشار
- **پیش‌نویس:** در سایت عمومی دیده نمی‌شود و در نقشه سایت نیست.
- **زمان‌بندی:** با رسیدن تاریخ، دستور `content:publish-scheduled` (روی cron) آن را منتشر می‌کند.
- **منتشرشده:** روی سایت و در `sitemap.xml`.

---

## ۸) SEO

- HTML معنایی، دقیقاً یک `<h1>` در هر صفحه، سلسله‌مراتب صحیح تیترها.
- متا: Title (با الگوی قابل‌تنظیم)، Description، Canonical، Robots، Open Graph، Twitter Card — همه از پنل قابل ویرایش (تب سئو).
- **JSON-LD:** Organization + WebSite در همه‌ی صفحات؛ BreadcrumbList، Article (مقالات)، Product/Service، FAQPage.
- `GET /sitemap.xml` — پویا، فقط محتوای منتشرشده، با کش (بازسازی: دکمه‌ی پنل یا `php artisan sitemap:generate`).
- `GET /robots.txt` — پویا، `Disallow: /admin`، اشاره به sitemap؛ خطوط اضافی در «تنظیمات ← سئو».
- همه‌ی پاسخ‌های `/admin` هدر `X-Robots-Tag: noindex` دارند.
- URLها فقط با slug (بدون id).

### افزودن Google Analytics / Tag Manager / Search Console
«تنظیمات سایت ← اسکریپت‌ها و آنالیتیکس» — کدها را وارد کنید؛ به‌صورت خودکار و شرطی در `<head>`/`<body>` تزریق می‌شوند.

---

## ۹) دستورهای مفید

```bash
php artisan migrate:fresh --seed        # بازساخت کامل دیتابیس + محتوای نمونه
php artisan content:publish-scheduled   # انتشار دستی محتوای زمان‌بندی‌شده
php artisan sitemap:generate            # بازسازی نقشه سایت
php artisan test                        # اجرای تست‌ها (PHPUnit، sqlite in-memory)
php artisan optimize:clear              # پاک‌کردن همه‌ی کش‌ها
```

---

## ۱۰) ساختار پروژه (خلاصه)

```
app/
  Http/Controllers/Public/   کنترلرهای سایت عمومی
  Http/Controllers/Admin/    کنترلرهای پنل (CMS)
  Models/                    مدل‌ها + Concerns (HasSlug, HasPublishing, HasLocale, Mediable, HasSeoMeta)
  Services/Cms/              SectionRegistry, SectionRenderer, MenuBuilder
  Services/Media/            MediaUploader (تولید نسخه‌ها)
  Services/Seo/              SeoResolver, SitemapBuilder
  Services/Settings/         SettingsRepository (کش تنظیمات)
config/cms.php               رجیستری ۱۸ نوع بخش + قواعد رسانه
config/site.php              اسکیمای تنظیمات سایت
resources/views/
  layouts/public.blade.php   قالب سایت
  partials/  components/  sections/   ← بلوک‌های Blade سایت
  public/                    صفحات سایت
resources/js/                پنل Inertia/React (app: admin.tsx)
routes/web.php · admin.php · pages.php
```

---

ساخته‌شده با ❤️ برای راگا سافت‌ور.
