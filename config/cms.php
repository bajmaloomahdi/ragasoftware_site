<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    | The site is Persian-only today. `supported` is the seam for adding more
    | languages later without a schema change (content tables carry `locale`).
    */
    'locales' => [
        'default' => 'fa',
        'supported' => ['fa'],
        'names' => ['fa' => 'فارسی', 'en' => 'English'],
        'rtl' => ['fa', 'ar', 'he'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Media library
    |--------------------------------------------------------------------------
    */
    'media' => [
        'disk' => 'uploads',
        'max_upload_kb' => (int) env('MEDIA_MAX_UPLOAD_KB', 4096),
        'image_mimes' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'],
        'document_mimes' => ['pdf'],
        // Responsive variants generated on upload (width in px). A matching
        // WebP is produced for every raster variant + the original.
        'variants' => [
            'thumb' => 240,
            'sm' => 480,
            'md' => 768,
            'lg' => 1280,
            'xl' => 1920,
        ],
        'webp_quality' => 78,
        'jpeg_quality' => 82,
    ],

    /*
    |--------------------------------------------------------------------------
    | Publishing
    |--------------------------------------------------------------------------
    */
    'statuses' => ['draft', 'scheduled', 'published'],

    /*
    |--------------------------------------------------------------------------
    | Section anchors
    |--------------------------------------------------------------------------
    | Maps a section *type* to a stable HTML id, so the homepage becomes a
    | single-page experience the header nav can scroll to. The renderer only
    | applies the id to the FIRST section of each type on a page.
    */
    'anchors' => [
        'hero' => 'home',
        'about_intro' => 'about',
        'services_grid' => 'services',
        'products_grid' => 'products',
        'advantages' => 'why',
        'stats' => 'stats',
        'projects_grid' => 'projects',
        'customers_logos' => 'customers',
        'testimonials' => 'testimonials',
        'team_grid' => 'team',
        'blog_latest' => 'articles',
        'faq' => 'faq',
        'cta_banner' => 'contact',
        'contact_form' => 'contact',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section builder registry
    |--------------------------------------------------------------------------
    | The catalogue of section *types* a page can be composed of. This is the
    | one thing that legitimately lives in code (structure). WHICH sections a
    | page uses, their ORDER and their CONTENT all live in the database
    | (`page_sections`). Each entry:
    |   label   – admin display name
    |   icon    – Ant Design icon name (admin)
    |   view    – blade partial under resources/views/sections/
    |   fields  – schema that drives the admin editing form + validation
    |   repeatable / source – dynamic content the resolver hydrates
    */
    'sections' => [

        'hero' => [
            'label' => 'هیرو (بخش نخست)',
            'icon' => 'RocketOutlined',
            'view' => 'hero',
            'fields' => [
                'eyebrow' => ['type' => 'text', 'label' => 'برچسب بالای عنوان'],
                'heading' => ['type' => 'text', 'label' => 'عنوان اصلی', 'required' => true],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح کوتاه'],
                'primary_cta_label' => ['type' => 'text', 'label' => 'متن دکمه اصلی'],
                'primary_cta_url' => ['type' => 'text', 'label' => 'لینک دکمه اصلی'],
                'secondary_cta_label' => ['type' => 'text', 'label' => 'متن دکمه دوم'],
                'secondary_cta_url' => ['type' => 'text', 'label' => 'لینک دکمه دوم'],
                'media_id' => ['type' => 'media', 'label' => 'تصویر / ماک‌آپ محصول'],
                'logo_cards' => ['type' => 'media_multiple', 'label' => 'لوگوهای کارت‌های هیرو (تا ۳ عدد)'],
                'stats' => ['type' => 'repeater', 'label' => 'آمار', 'item' => [
                    'value' => ['type' => 'text', 'label' => 'عدد'],
                    'label' => ['type' => 'text', 'label' => 'برچسب'],
                ]],
            ],
        ],

        'about_intro' => [
            'label' => 'معرفی کوتاه شرکت',
            'icon' => 'InfoCircleOutlined',
            'view' => 'about_intro',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'body' => ['type' => 'richtext', 'label' => 'متن معرفی'],
                'media_id' => ['type' => 'media', 'label' => 'تصویر'],
                'cta_label' => ['type' => 'text', 'label' => 'متن دکمه'],
                'cta_url' => ['type' => 'text', 'label' => 'لینک دکمه'],
            ],
        ],

        'rich_text' => [
            'label' => 'متن آزاد',
            'icon' => 'FileTextOutlined',
            'view' => 'rich_text',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'body' => ['type' => 'richtext', 'label' => 'محتوا', 'required' => true],
                'width' => ['type' => 'select', 'label' => 'عرض', 'options' => ['narrow' => 'باریک', 'normal' => 'معمولی', 'wide' => 'عریض']],
            ],
        ],

        'products_grid' => [
            'label' => 'شبکه محصولات / راهکارها',
            'icon' => 'AppstoreOutlined',
            'view' => 'products_grid',
            'source' => 'products',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'mode' => ['type' => 'select', 'label' => 'انتخاب', 'options' => ['featured' => 'محصولات ویژه', 'all' => 'همه', 'custom' => 'انتخابی']],
                'item_ids' => ['type' => 'relation', 'label' => 'محصولات', 'resource' => 'products', 'when' => ['mode' => 'custom']],
                'limit' => ['type' => 'number', 'label' => 'حداکثر تعداد', 'default' => 6],
                'columns' => ['type' => 'select', 'label' => 'ستون‌ها', 'options' => ['2' => '۲', '3' => '۳', '4' => '۴'], 'default' => '3'],
            ],
        ],

        'services_grid' => [
            'label' => 'شبکه خدمات',
            'icon' => 'ToolOutlined',
            'view' => 'services_grid',
            'source' => 'services',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'mode' => ['type' => 'select', 'label' => 'انتخاب', 'options' => ['featured' => 'ویژه', 'all' => 'همه', 'custom' => 'انتخابی']],
                'item_ids' => ['type' => 'relation', 'label' => 'خدمات', 'resource' => 'services', 'when' => ['mode' => 'custom']],
                'limit' => ['type' => 'number', 'label' => 'حداکثر تعداد', 'default' => 6],
            ],
        ],

        'features' => [
            'label' => 'ویژگی‌ها',
            'icon' => 'CheckSquareOutlined',
            'view' => 'features',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'items' => ['type' => 'repeater', 'label' => 'موارد', 'item' => [
                    'icon' => ['type' => 'text', 'label' => 'آیکن'],
                    'title' => ['type' => 'text', 'label' => 'عنوان'],
                    'description' => ['type' => 'textarea', 'label' => 'توضیح'],
                ]],
                'columns' => ['type' => 'select', 'label' => 'ستون‌ها', 'options' => ['2' => '۲', '3' => '۳', '4' => '۴'], 'default' => '3'],
            ],
        ],

        'advantages' => [
            'label' => 'چرا راگا سافت‌ور؟ (مزیت‌ها)',
            'icon' => 'TrophyOutlined',
            'view' => 'advantages',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'media_id' => ['type' => 'media', 'label' => 'تصویر کناری'],
                'items' => ['type' => 'repeater', 'label' => 'مزیت‌ها', 'item' => [
                    'icon' => ['type' => 'text', 'label' => 'آیکن'],
                    'title' => ['type' => 'text', 'label' => 'عنوان'],
                    'description' => ['type' => 'textarea', 'label' => 'توضیح'],
                ]],
            ],
        ],

        'projects_grid' => [
            'label' => 'نمونه‌کارها',
            'icon' => 'ProjectOutlined',
            'view' => 'projects_grid',
            'source' => 'projects',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'limit' => ['type' => 'number', 'label' => 'حداکثر تعداد', 'default' => 6],
            ],
        ],

        'customers_logos' => [
            'label' => 'لوگوی مشتریان',
            'icon' => 'TeamOutlined',
            'view' => 'customers_logos',
            'source' => 'customers',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'grayscale' => ['type' => 'boolean', 'label' => 'نمایش سیاه‌وسفید', 'default' => true],
            ],
        ],

        'testimonials' => [
            'label' => 'نظرات مشتریان',
            'icon' => 'CommentOutlined',
            'view' => 'testimonials',
            'source' => 'testimonials',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'limit' => ['type' => 'number', 'label' => 'حداکثر تعداد', 'default' => 3],
            ],
        ],

        'team_grid' => [
            'label' => 'اعضای تیم',
            'icon' => 'IdcardOutlined',
            'view' => 'team_grid',
            'source' => 'team',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
            ],
        ],

        'blog_latest' => [
            'label' => 'آخرین مقالات',
            'icon' => 'ReadOutlined',
            'view' => 'blog_latest',
            'source' => 'blog',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'limit' => ['type' => 'number', 'label' => 'تعداد', 'default' => 3],
                'category_id' => ['type' => 'relation', 'label' => 'فقط دسته', 'resource' => 'blog-categories'],
            ],
        ],

        'faq' => [
            'label' => 'سؤالات متداول',
            'icon' => 'QuestionCircleOutlined',
            'view' => 'faq',
            'source' => 'faqs',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'category_id' => ['type' => 'relation', 'label' => 'دسته', 'resource' => 'faq-categories'],
                'limit' => ['type' => 'number', 'label' => 'تعداد', 'default' => 8],
            ],
        ],

        'stats' => [
            'label' => 'آمار و ارقام',
            'icon' => 'BarChartOutlined',
            'view' => 'stats',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'items' => ['type' => 'repeater', 'label' => 'آمار', 'item' => [
                    'value' => ['type' => 'text', 'label' => 'عدد'],
                    'label' => ['type' => 'text', 'label' => 'برچسب'],
                ]],
            ],
        ],

        'gallery' => [
            'label' => 'گالری تصاویر',
            'icon' => 'PictureOutlined',
            'view' => 'gallery',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'media_ids' => ['type' => 'media_multiple', 'label' => 'تصاویر'],
            ],
        ],

        'cta_banner' => [
            'label' => 'بنر فراخوان (CTA)',
            'icon' => 'NotificationOutlined',
            'view' => 'cta_banner',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان', 'required' => true],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'cta_label' => ['type' => 'text', 'label' => 'متن دکمه'],
                'cta_url' => ['type' => 'text', 'label' => 'لینک دکمه'],
                'style' => ['type' => 'select', 'label' => 'ظاهر', 'options' => ['gradient' => 'گرادیان (پیش‌فرض)', 'light' => 'روشن', 'solid' => 'تیره یکدست']],
            ],
        ],

        'contact_form' => [
            'label' => 'فرم تماس',
            'icon' => 'MailOutlined',
            'view' => 'contact_form',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'subheading' => ['type' => 'textarea', 'label' => 'توضیح'],
                'show_contact_info' => ['type' => 'boolean', 'label' => 'نمایش اطلاعات تماس', 'default' => true],
            ],
        ],

        'logo_cloud' => [
            'label' => 'ابر لوگو / فناوری‌ها',
            'icon' => 'CloudOutlined',
            'view' => 'logo_cloud',
            'fields' => [
                'heading' => ['type' => 'text', 'label' => 'عنوان'],
                'media_ids' => ['type' => 'media_multiple', 'label' => 'لوگوها'],
            ],
        ],
    ],
];
