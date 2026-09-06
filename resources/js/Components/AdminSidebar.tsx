import { router, usePage } from '@inertiajs/react';
import { Layout, Menu, Drawer } from 'antd';
import type { MenuProps } from 'antd';
import {
    DashboardOutlined,
    SettingOutlined,
    HomeOutlined,
    FileTextOutlined,
    AppstoreOutlined,
    ToolOutlined,
    ProjectOutlined,
    TeamOutlined,
    ReadOutlined,
    TagsOutlined,
    QuestionCircleOutlined,
    MenuOutlined,
    PictureOutlined,
    SearchOutlined,
    MailOutlined,
    UserOutlined,
    CommentOutlined,
    ShopOutlined,
} from '@ant-design/icons';
import type { PageProps } from '@/types/global';

const { Sider } = Layout;

type Item = Required<MenuProps>['items'][number];

/** The single «مدیریت وب‌سایت» navigation tree. `key` is a Ziggy route name. */
const ITEMS: Item[] = [
    { key: 'admin.dashboard', icon: <DashboardOutlined />, label: 'داشبورد' },
    { type: 'divider' },
    { key: 'admin.settings.edit', icon: <SettingOutlined />, label: 'تنظیمات سایت' },
    { key: 'admin.homepage.edit', icon: <HomeOutlined />, label: 'صفحه اصلی' },
    { key: 'admin.pages.index', icon: <FileTextOutlined />, label: 'صفحات' },
    {
        key: 'grp-catalog',
        icon: <AppstoreOutlined />,
        label: 'محصولات و خدمات',
        children: [
            { key: 'admin.products.index', icon: <ShopOutlined />, label: 'محصولات' },
            { key: 'admin.product-categories.index', icon: <TagsOutlined />, label: 'دسته‌بندی محصولات' },
            { key: 'admin.services.index', icon: <ToolOutlined />, label: 'خدمات' },
        ],
    },
    {
        key: 'grp-showcase',
        icon: <ProjectOutlined />,
        label: 'نمونه‌کارها و مشتریان',
        children: [
            { key: 'admin.projects.index', icon: <ProjectOutlined />, label: 'نمونه‌کارها' },
            { key: 'admin.customers.index', icon: <TeamOutlined />, label: 'مشتریان' },
            { key: 'admin.testimonials.index', icon: <CommentOutlined />, label: 'نظرات مشتریان' },
            { key: 'admin.team.index', icon: <TeamOutlined />, label: 'اعضای تیم' },
        ],
    },
    {
        key: 'grp-blog',
        icon: <ReadOutlined />,
        label: 'وبلاگ',
        children: [
            { key: 'admin.posts.index', icon: <ReadOutlined />, label: 'مقالات' },
            { key: 'admin.blog-categories.index', icon: <TagsOutlined />, label: 'دسته‌بندی مقالات' },
            { key: 'admin.blog-tags.index', icon: <TagsOutlined />, label: 'برچسب‌ها' },
        ],
    },
    { key: 'admin.faqs.index', icon: <QuestionCircleOutlined />, label: 'سؤالات متداول' },
    { key: 'admin.menus.index', icon: <MenuOutlined />, label: 'منوها' },
    { key: 'admin.media.index', icon: <PictureOutlined />, label: 'رسانه‌ها' },
    { key: 'admin.seo.edit', icon: <SearchOutlined />, label: 'SEO و ریدایرکت' },
    { key: 'admin.messages.index', icon: <MailOutlined />, label: 'پیام‌های تماس' },
    { type: 'divider' },
    { key: 'admin.users.index', icon: <UserOutlined />, label: 'کاربران و نقش‌ها' },
];

function useMenu() {
    const { props } = usePage<PageProps>();
    const current = (props.ziggy?.location as string) || '';

    const onClick: MenuProps['onClick'] = ({ key }) => {
        if (key.startsWith('grp-')) return;
        try {
            router.visit(route(key));
        } catch {
            /* route not registered yet (built incrementally) */
        }
    };

    return { onClick, current };
}

function Nav() {
    const { onClick } = useMenu();
    return (
        <Menu
            theme="dark"
            mode="inline"
            items={ITEMS}
            onClick={onClick}
            defaultSelectedKeys={['admin.dashboard']}
            style={{ borderInlineEnd: 0 }}
        />
    );
}

export default function AdminSidebar({
    isMobile,
    collapsed,
    drawerOpen,
    onCloseDrawer,
}: {
    isMobile: boolean;
    collapsed: boolean;
    drawerOpen: boolean;
    onCloseDrawer: () => void;
}) {
    const brand = (
        <div
            style={{
                height: 56,
                margin: 16,
                display: 'flex',
                alignItems: 'center',
                gap: 8,
                color: '#fff',
                fontWeight: 700,
                fontSize: 18,
            }}
        >
            <MenuOutlined /> راگا سافت‌ور
        </div>
    );

    if (isMobile) {
        return (
            <Drawer
                placement="right"
                open={drawerOpen}
                onClose={onCloseDrawer}
                width={264}
                styles={{ body: { padding: 0, background: '#0b1b34' }, header: { display: 'none' } }}
            >
                {brand}
                <Nav />
            </Drawer>
        );
    }

    return (
        <Sider
            collapsible
            collapsed={collapsed}
            trigger={null}
            width={264}
            style={{ position: 'sticky', insetBlockStart: 0, height: '100vh', overflow: 'auto' }}
        >
            {brand}
            <Nav />
        </Sider>
    );
}
