# RagaSoftware — وب‌سایت رسمی (`ragasoftware_site`)

وب‌سایت رسمی و سیستم مدیریت محتوای شرکت راگا سافت‌ور.

- **بک‌اند:** Laravel 13 (PHP 8.3+)
- **سایت عمومی:** Blade + Tailwind CSS v4 + Alpine.js (رندر سمت سرور، SEO کامل)
- **پنل مدیریت:** Inertia 2 + React 19 + TypeScript + Ant Design (زیر مسیر `/admin`)
- **پایگاه‌داده:** MySQL 8 (در توسعه: Docker Compose)
- **بیلد:** Vite

> اصل بنیادی پروژه: هیچ محتوای قابل‌مشاهده‌ی سایت در کد ثابت نیست؛ همه‌چیز از پایگاه‌داده خوانده و از پنل مدیریت ویرایش می‌شود.

راهنمای کامل نصب، بیلد، استقرار روی هاست اشتراکی و پشتیبان‌گیری در فاز پایانی به همین فایل افزوده می‌شود.

## راه‌اندازی سریع توسعه

```bash
cp .env.example .env
# تنظیم مقادیر DB_* و SEED_ADMIN_*

docker compose up -d mysql          # فقط پایگاه‌داده
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev                         # سرور توسعه Vite
php artisan serve                   # http://localhost:8000
```

اجرای کامل با Docker:

```bash
docker compose up -d                # app + nginx + mysql + vite + adminer
# سایت:   http://localhost:8080
# ادمین:  http://localhost:8080/admin
# Adminer: http://localhost:8081
```
