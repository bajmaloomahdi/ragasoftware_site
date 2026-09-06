<?php

/*
|--------------------------------------------------------------------------
| Editable site settings — schema
|--------------------------------------------------------------------------
| Declares every key/value setting the admin can edit under
| «تنظیمات سایت». The seeder creates the rows with defaults; the admin
| Settings screen is generated from this schema. Type drives the input
| widget + the cast in SiteSetting::castValue().
|
| type: string | text | html | boolean | number | media | json
*/

return [

    'groups' => [
        'general' => 'عمومی',
        'contact' => 'اطلاعات تماس',
        'footer' => 'فوتر',
        'analytics' => 'اسکریپت‌ها و آنالیتیکس',
        'seo' => 'سئو',
        'appearance' => 'ظاهر',
    ],

    'fields' => [
        // --- general ---
        ['key' => 'general.company_name', 'group' => 'general', 'type' => 'string', 'label' => 'نام شرکت', 'default' => 'راگا سافت‌ور'],
        ['key' => 'general.company_legal_name', 'group' => 'general', 'type' => 'string', 'label' => 'نام حقوقی', 'default' => 'شرکت مهندسی نرم‌افزار راگا'],
        ['key' => 'general.tagline', 'group' => 'general', 'type' => 'string', 'label' => 'شعار کوتاه', 'default' => 'نرم‌افزار سازمانی، ساده و قابل‌اتکا'],
        ['key' => 'general.short_about', 'group' => 'general', 'type' => 'text', 'label' => 'معرفی کوتاه', 'default' => 'راگا سافت‌ور، توسعه‌دهنده راهکارهای نرم‌افزاری سازمانی برای کسب‌وکارهای ایرانی است.'],
        ['key' => 'general.logo_media_id', 'group' => 'general', 'type' => 'media', 'label' => 'لوگو'],
        ['key' => 'general.logo_light_media_id', 'group' => 'general', 'type' => 'media', 'label' => 'لوگوی روشن (برای پس‌زمینه تیره)'],
        ['key' => 'general.favicon_media_id', 'group' => 'general', 'type' => 'media', 'label' => 'فاوآیکن'],

        // --- contact ---
        ['key' => 'contact.phone', 'group' => 'contact', 'type' => 'string', 'label' => 'تلفن', 'default' => '021-00000000'],
        ['key' => 'contact.mobile', 'group' => 'contact', 'type' => 'string', 'label' => 'موبایل / پشتیبانی', 'default' => ''],
        ['key' => 'contact.email', 'group' => 'contact', 'type' => 'string', 'label' => 'ایمیل', 'default' => 'info@ragasoftware.ir'],
        ['key' => 'contact.sales_email', 'group' => 'contact', 'type' => 'string', 'label' => 'ایمیل فروش', 'default' => 'sales@ragasoftware.ir'],
        ['key' => 'contact.address', 'group' => 'contact', 'type' => 'text', 'label' => 'نشانی', 'default' => 'تهران، ایران'],
        ['key' => 'contact.map_embed', 'group' => 'contact', 'type' => 'text', 'label' => 'کد نقشه (iframe)', 'default' => ''],
        ['key' => 'contact.working_hours', 'group' => 'contact', 'type' => 'string', 'label' => 'ساعات کاری', 'default' => 'شنبه تا چهارشنبه، ۹ تا ۱۷'],

        // --- footer ---
        ['key' => 'footer.about_text', 'group' => 'footer', 'type' => 'text', 'label' => 'متن معرفی در فوتر', 'default' => 'راگا سافت‌ور؛ همراه سازمان شما در مسیر تحول دیجیتال با نرم‌افزارهای یکپارچه و بومی.'],
        ['key' => 'footer.copyright', 'group' => 'footer', 'type' => 'string', 'label' => 'متن کپی‌رایت', 'default' => 'کلیه حقوق برای راگا سافت‌ور محفوظ است.'],
        ['key' => 'footer.newsletter_enabled', 'group' => 'footer', 'type' => 'boolean', 'label' => 'نمایش فرم خبرنامه', 'default' => false],

        // --- analytics / scripts ---
        ['key' => 'analytics.ga4_id', 'group' => 'analytics', 'type' => 'string', 'label' => 'شناسه Google Analytics 4 (G-XXXX)', 'default' => ''],
        ['key' => 'analytics.gtm_id', 'group' => 'analytics', 'type' => 'string', 'label' => 'شناسه Google Tag Manager (GTM-XXXX)', 'default' => ''],
        ['key' => 'analytics.search_console_verification', 'group' => 'analytics', 'type' => 'string', 'label' => 'کد تأیید Search Console', 'default' => ''],
        ['key' => 'analytics.head_scripts', 'group' => 'analytics', 'type' => 'text', 'label' => 'اسکریپت‌های اضافی <head>', 'default' => ''],
        ['key' => 'analytics.body_scripts', 'group' => 'analytics', 'type' => 'text', 'label' => 'اسکریپت‌های انتهای <body>', 'default' => ''],

        // --- seo defaults ---
        ['key' => 'seo.default_title', 'group' => 'seo', 'type' => 'string', 'label' => 'عنوان پیش‌فرض سایت', 'default' => 'راگا سافت‌ور | نرم‌افزار سازمانی، ERP و CRM'],
        ['key' => 'seo.title_template', 'group' => 'seo', 'type' => 'string', 'label' => 'الگوی عنوان (:title جایگزین می‌شود)', 'default' => ':title | راگا سافت‌ور'],
        ['key' => 'seo.default_description', 'group' => 'seo', 'type' => 'text', 'label' => 'توضیح متای پیش‌فرض', 'default' => 'راگا سافت‌ور؛ طراحی، توسعه و پیاده‌سازی نرم‌افزارهای سازمانی شامل ERP، CRM، اتوماسیون اداری، مدیریت پروژه و داشبورد مدیریتی و هوش تجاری.'],
        ['key' => 'seo.default_og_media_id', 'group' => 'seo', 'type' => 'media', 'label' => 'تصویر پیش‌فرض اشتراک‌گذاری'],
        ['key' => 'seo.robots_extra', 'group' => 'seo', 'type' => 'text', 'label' => 'خطوط اضافی robots.txt', 'default' => ''],
        ['key' => 'seo.organization_type', 'group' => 'seo', 'type' => 'string', 'label' => 'نوع Organization Schema', 'default' => 'Organization'],

        // --- appearance ---
        ['key' => 'appearance.primary_color', 'group' => 'appearance', 'type' => 'string', 'label' => 'رنگ اصلی (Accent)', 'default' => '#2563eb'],
        ['key' => 'appearance.show_top_bar', 'group' => 'appearance', 'type' => 'boolean', 'label' => 'نمایش نوار بالای سایت', 'default' => true],
        ['key' => 'appearance.top_bar_text', 'group' => 'appearance', 'type' => 'string', 'label' => 'متن نوار بالا', 'default' => 'مشاوره رایگان پیاده‌سازی نرم‌افزار سازمانی'],
    ],
];
