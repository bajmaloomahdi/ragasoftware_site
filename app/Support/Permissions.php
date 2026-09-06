<?php

namespace App\Support;

/**
 * The single source of truth for the admin permission catalogue.
 *
 * `groups()` drives both the seeder and the admin "Roles" permission-matrix UI.
 * Permission names follow "<area>.<action>".
 */
final class Permissions
{
    /**
     * @return array<string, array{label: string, permissions: array<string, string>}>
     */
    public static function groups(): array
    {
        return [
            'dashboard' => [
                'label' => 'داشبورد',
                'permissions' => [
                    'dashboard.view' => 'مشاهده داشبورد',
                ],
            ],
            'settings' => [
                'label' => 'تنظیمات سایت',
                'permissions' => [
                    'settings.manage' => 'مدیریت تنظیمات سایت',
                ],
            ],
            'pages' => [
                'label' => 'صفحات و بخش‌ها',
                'permissions' => [
                    'pages.view' => 'مشاهده صفحات',
                    'pages.manage' => 'ایجاد و ویرایش صفحات و بخش‌ها',
                    'pages.publish' => 'انتشار / لغو انتشار صفحات',
                    'pages.delete' => 'حذف صفحات',
                ],
            ],
            'products' => [
                'label' => 'محصولات',
                'permissions' => [
                    'products.view' => 'مشاهده محصولات',
                    'products.manage' => 'ایجاد و ویرایش محصولات',
                    'products.publish' => 'انتشار محصولات',
                    'products.delete' => 'حذف محصولات',
                ],
            ],
            'services' => [
                'label' => 'خدمات',
                'permissions' => [
                    'services.view' => 'مشاهده خدمات',
                    'services.manage' => 'ایجاد و ویرایش خدمات',
                    'services.publish' => 'انتشار خدمات',
                    'services.delete' => 'حذف خدمات',
                ],
            ],
            'projects' => [
                'label' => 'نمونه‌کارها',
                'permissions' => [
                    'projects.view' => 'مشاهده نمونه‌کارها',
                    'projects.manage' => 'ایجاد و ویرایش نمونه‌کارها',
                    'projects.publish' => 'انتشار نمونه‌کارها',
                    'projects.delete' => 'حذف نمونه‌کارها',
                ],
            ],
            'customers' => [
                'label' => 'مشتریان و نظرات',
                'permissions' => [
                    'customers.manage' => 'مدیریت مشتریان و نظرات',
                ],
            ],
            'team' => [
                'label' => 'اعضای تیم',
                'permissions' => [
                    'team.manage' => 'مدیریت اعضای تیم',
                ],
            ],
            'blog' => [
                'label' => 'وبلاگ',
                'permissions' => [
                    'blog.view' => 'مشاهده مقالات',
                    'blog.manage' => 'ایجاد و ویرایش مقالات، دسته‌ها و برچسب‌ها',
                    'blog.publish' => 'انتشار مقالات',
                    'blog.delete' => 'حذف مقالات',
                ],
            ],
            'faq' => [
                'label' => 'سؤالات متداول',
                'permissions' => [
                    'faq.manage' => 'مدیریت سؤالات متداول',
                ],
            ],
            'menus' => [
                'label' => 'منوها',
                'permissions' => [
                    'menus.manage' => 'مدیریت منوها',
                ],
            ],
            'media' => [
                'label' => 'رسانه‌ها',
                'permissions' => [
                    'media.view' => 'مشاهده کتابخانه رسانه',
                    'media.upload' => 'بارگذاری رسانه',
                    'media.delete' => 'حذف رسانه',
                ],
            ],
            'seo' => [
                'label' => 'SEO و ریدایرکت',
                'permissions' => [
                    'seo.manage' => 'مدیریت SEO، ریدایرکت و robots',
                ],
            ],
            'messages' => [
                'label' => 'پیام‌های تماس',
                'permissions' => [
                    'messages.view' => 'مشاهده پیام‌ها',
                    'messages.manage' => 'تغییر وضعیت و حذف پیام‌ها',
                ],
            ],
            'users' => [
                'label' => 'کاربران و نقش‌ها',
                'permissions' => [
                    'users.view' => 'مشاهده کاربران و نقش‌ها',
                    'users.manage' => 'مدیریت کاربران، نقش‌ها و دسترسی‌ها',
                ],
            ],
        ];
    }

    /** @return list<string> */
    public static function all(): array
    {
        $out = [];
        foreach (self::groups() as $group) {
            foreach (array_keys($group['permissions']) as $name) {
                $out[] = $name;
            }
        }

        return $out;
    }

    /** Content-editor bundle (no settings / menus / seo / users). */
    public static function editorBundle(): array
    {
        return array_values(array_filter(self::all(), function (string $p) {
            foreach (['settings.', 'menus.', 'seo.', 'users.'] as $deny) {
                if (str_starts_with($p, $deny)) {
                    return false;
                }
            }

            return ! str_ends_with($p, '.delete');
        }));
    }

    /** Blog-only author bundle. */
    public static function authorBundle(): array
    {
        return ['dashboard.view', 'blog.view', 'blog.manage', 'media.view', 'media.upload'];
    }
}
