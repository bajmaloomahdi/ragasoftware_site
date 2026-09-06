import type { ThemeConfig } from 'antd';

/**
 * Central Ant Design theme tokens for the admin panel.
 * The public website has its own, independent visual identity (Tailwind).
 */
export const BRAND = {
    primary: '#2563eb',
    navy: '#0b1b34',
    cyan: '#06b6d4',
    success: '#16a34a',
    warning: '#d97706',
    error: '#dc2626',
};

export const adminTheme: ThemeConfig = {
    token: {
        colorPrimary: BRAND.primary,
        colorInfo: BRAND.primary,
        borderRadius: 8,
        fontFamily: "'Vazirmatn', 'Segoe UI', Tahoma, sans-serif",
        fontSize: 14,
    },
    components: {
        Layout: {
            siderBg: BRAND.navy,
            triggerBg: '#071324',
        },
        Menu: {
            darkItemBg: BRAND.navy,
            darkSubMenuItemBg: '#071324',
            darkItemSelectedBg: BRAND.primary,
        },
    },
};

/** Shared status → Ant tag colour map used across CMS list screens. */
export const STATUS_COLORS: Record<string, string> = {
    draft: 'default',
    scheduled: 'gold',
    published: 'green',
    new: 'blue',
    read: 'cyan',
    answered: 'green',
    closed: 'default',
};
