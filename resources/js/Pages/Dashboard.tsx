import type { ReactNode } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Card, Col, Row, Statistic, Table, Tag, Typography } from 'antd';
import {
    FileTextOutlined,
    ShopOutlined,
    ToolOutlined,
    ProjectOutlined,
    ReadOutlined,
    PictureOutlined,
    MailOutlined,
} from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import { STATUS_COLORS } from '@/theme';
import type { PageProps } from '@/types/global';

interface Stats {
    pages: number;
    products: number;
    services: number;
    projects: number;
    posts: number;
    draftPosts: number;
    media: number;
    newMessages: number;
}

type Props = PageProps<{
    stats: Stats;
    recentMessages: Array<{ id: number; name: string; subject: string | null; status: string; created_at: string }>;
    recentPosts: Array<{ id: number; title: string; status: string; published_at: string | null; updated_at: string }>;
}>;

const STATUS_LABEL: Record<string, string> = {
    draft: 'پیش‌نویس',
    scheduled: 'زمان‌بندی‌شده',
    published: 'منتشرشده',
    new: 'جدید',
    read: 'خوانده‌شده',
    answered: 'پاسخ داده‌شده',
    closed: 'بسته‌شده',
};

function tile(icon: ReactNode, title: string, value: number, routeName: string) {
    return (
        <Col xs={12} sm={8} lg={6}>
            <Card hoverable onClick={() => tryVisit(routeName)} styles={{ body: { padding: 16 } }}>
                <Statistic title={title} value={value} prefix={icon} />
            </Card>
        </Col>
    );
}

function tryVisit(name: string) {
    try {
        router.visit(route(name));
    } catch {
        /* route added in a later phase */
    }
}

export default function Dashboard({ stats, recentMessages, recentPosts }: Props) {
    return (
        <AdminLayout>
            <Head title="داشبورد" />
            <Typography.Title level={3} style={{ marginTop: 0 }}>
                داشبورد مدیریت وب‌سایت
            </Typography.Title>

            <Row gutter={[16, 16]}>
                {tile(<FileTextOutlined />, 'صفحات', stats.pages, 'admin.pages.index')}
                {tile(<ShopOutlined />, 'محصولات', stats.products, 'admin.products.index')}
                {tile(<ToolOutlined />, 'خدمات', stats.services, 'admin.services.index')}
                {tile(<ProjectOutlined />, 'نمونه‌کارها', stats.projects, 'admin.projects.index')}
                {tile(<ReadOutlined />, 'مقالات', stats.posts, 'admin.posts.index')}
                {tile(<PictureOutlined />, 'رسانه‌ها', stats.media, 'admin.media.index')}
                {tile(<MailOutlined />, 'پیام‌های جدید', stats.newMessages, 'admin.messages.index')}
            </Row>

            <Row gutter={[16, 16]} style={{ marginTop: 24 }}>
                <Col xs={24} lg={12}>
                    <Card title="آخرین پیام‌های تماس">
                        <Table
                            size="small"
                            rowKey="id"
                            pagination={false}
                            dataSource={recentMessages}
                            locale={{ emptyText: 'پیامی ثبت نشده است' }}
                            columns={[
                                { title: 'فرستنده', dataIndex: 'name' },
                                { title: 'موضوع', dataIndex: 'subject', render: (v) => v || '—' },
                                {
                                    title: 'وضعیت',
                                    dataIndex: 'status',
                                    render: (s: string) => <Tag color={STATUS_COLORS[s]}>{STATUS_LABEL[s] ?? s}</Tag>,
                                },
                            ]}
                        />
                    </Card>
                </Col>
                <Col xs={24} lg={12}>
                    <Card title="آخرین مقالات">
                        <Table
                            size="small"
                            rowKey="id"
                            pagination={false}
                            dataSource={recentPosts}
                            locale={{ emptyText: 'مقاله‌ای ثبت نشده است' }}
                            columns={[
                                { title: 'عنوان', dataIndex: 'title' },
                                {
                                    title: 'وضعیت',
                                    dataIndex: 'status',
                                    render: (s: string) => <Tag color={STATUS_COLORS[s]}>{STATUS_LABEL[s] ?? s}</Tag>,
                                },
                            ]}
                        />
                    </Card>
                </Col>
            </Row>

            <div style={{ marginTop: 16 }}>
                <Link href="/" style={{ marginInlineEnd: 12 }}>
                    مشاهده وب‌سایت
                </Link>
            </div>
        </AdminLayout>
    );
}
