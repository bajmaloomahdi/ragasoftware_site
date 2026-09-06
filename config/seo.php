<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fallback SEO values
    |--------------------------------------------------------------------------
    | Used only when neither the per-entity `seo_meta` row nor the editable
    | site settings (group "seo") provide a value. Everything here is also
    | overridable from the admin panel — these are last-resort defaults.
    */
    'defaults' => [
        'title' => 'راگا سافت‌ور',
        'title_template' => ':title | راگا سافت‌ور',
        'description' => 'راگا سافت‌ور؛ طراحی و پیاده‌سازی نرم‌افزارهای سازمانی، ERP، CRM، اتوماسیون اداری و راهکارهای هوش تجاری.',
        'meta_robots' => 'index, follow',
        'twitter_card' => 'summary_large_image',
        'locale' => 'fa_IR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Structured data (JSON-LD)
    |--------------------------------------------------------------------------
    | Organization fields are seeded into site settings and editable there;
    | these keys document the shape the SeoResolver expects.
    */
    'organization' => [
        'type' => 'Organization',
        'name' => 'راگا سافت‌ور',
        'legal_name' => 'شرکت راگا سافت‌ور',
        'logo' => null,          // media URL, from settings
        'same_as' => [],         // social profile URLs, from social_links
    ],

    /*
    |--------------------------------------------------------------------------
    | Sitemap
    |--------------------------------------------------------------------------
    */
    'sitemap' => [
        'cache_minutes' => 60,
        'include' => ['pages', 'products', 'services', 'projects', 'blog_posts', 'blog_categories'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Robots.txt
    |--------------------------------------------------------------------------
    | Base rules; extra lines are appended from the editable "seo" settings.
    */
    'robots' => [
        'disallow' => ['/admin', '/admin/', '/login', '/storage/framework'],
    ],
];
