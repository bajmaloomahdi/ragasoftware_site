import { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Card, Button, Space, Table, Tag, Typography, Modal, Form, Input, Select, Switch, Popconfirm, App } from 'antd';
import { SearchOutlined, PlusOutlined, ReloadOutlined, DeleteOutlined, EditOutlined } from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Redirect {
    id: number;
    from_path: string;
    to_path: string;
    status_code: number;
    hits: number;
    is_active: boolean;
}

type Props = PageProps<{
    redirects: Paginated<Redirect>;
    sitemapUrl: string;
    robotsUrl: string;
}>;

export default function SeoEdit({ redirects, sitemapUrl, robotsUrl }: Props) {
    const { message } = App.useApp();
    const [editing, setEditing] = useState<Redirect | null>(null);
    const [open, setOpen] = useState(false);
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>({ from_path: '', to_path: '', status_code: 301, is_active: true });

    const start = (r: Redirect | null) => {
        setEditing(r);
        form.setData(r ?? { from_path: '', to_path: '', status_code: 301, is_active: true });
        setOpen(true);
    };
    const submit = () => {
        const opts = { onSuccess: () => setOpen(false), preserveScroll: true };
        if (editing) form.put(route('admin.seo.redirects.update', editing.id), opts);
        else form.post(route('admin.seo.redirects.store'), opts);
    };

    return (
        <AdminLayout>
            <Head title="SEO و ریدایرکت" />
            <PageHeader title="SEO و ریدایرکت" subtitle="نقشه سایت، robots و ریدایرکت‌های ۳۰۱/۳۰۲" icon={<SearchOutlined />} />

            <Card title="نقشه سایت و robots.txt" style={{ marginBottom: 16 }}>
                <Space direction="vertical">
                    <Typography.Text>
                        نقشه سایت: <a href={sitemapUrl} target="_blank" rel="noreferrer" dir="ltr">{sitemapUrl}</a>
                    </Typography.Text>
                    <Typography.Text>
                        robots.txt: <a href={robotsUrl} target="_blank" rel="noreferrer" dir="ltr">{robotsUrl}</a>
                    </Typography.Text>
                    <Typography.Paragraph type="secondary">
                        نقشه سایت به‌صورت خودکار از محتوای منتشرشده ساخته می‌شود. متن‌های اضافی robots و پیش‌فرض‌های سئو
                        در «تنظیمات سایت ← سئو» قابل ویرایش است.
                    </Typography.Paragraph>
                    <Button
                        icon={<ReloadOutlined />}
                        onClick={() =>
                            router.post(route('admin.seo.sitemap.rebuild'), {}, {
                                preserveScroll: true,
                                onSuccess: () => message.success('نقشه سایت بازسازی شد'),
                            })
                        }
                    >
                        بازسازی نقشه سایت
                    </Button>
                </Space>
            </Card>

            <Card
                title="ریدایرکت‌ها"
                extra={
                    <Button type="primary" icon={<PlusOutlined />} onClick={() => start(null)}>
                        افزودن ریدایرکت
                    </Button>
                }
            >
                <Table
                    rowKey="id"
                    dataSource={redirects.data}
                    pagination={{ current: redirects.current_page, total: redirects.total, pageSize: redirects.per_page }}
                    onChange={(p) => router.get(route('admin.seo.edit'), { page: p.current }, { preserveState: true })}
                    columns={[
                        { title: 'از', dataIndex: 'from_path', render: (v) => <span dir="ltr">{v}</span> },
                        { title: 'به', dataIndex: 'to_path', render: (v) => <span dir="ltr">{v}</span> },
                        { title: 'کد', dataIndex: 'status_code', width: 70 },
                        { title: 'بازدید', dataIndex: 'hits', width: 80 },
                        {
                            title: 'وضعیت',
                            dataIndex: 'is_active',
                            width: 90,
                            render: (v: boolean) => <Tag color={v ? 'green' : 'default'}>{v ? 'فعال' : 'غیرفعال'}</Tag>,
                        },
                        {
                            title: '',
                            width: 100,
                            render: (_, r: Redirect) => (
                                <Space size={2}>
                                    <Button type="text" icon={<EditOutlined />} onClick={() => start(r)} />
                                    <Popconfirm
                                        title="حذف؟"
                                        okText="حذف"
                                        cancelText="انصراف"
                                        onConfirm={() => router.delete(route('admin.seo.redirects.destroy', r.id))}
                                    >
                                        <Button type="text" danger icon={<DeleteOutlined />} />
                                    </Popconfirm>
                                </Space>
                            ),
                        },
                    ]}
                />
            </Card>

            <Modal open={open} onCancel={() => setOpen(false)} onOk={submit} okText="ذخیره" cancelText="انصراف" title="ریدایرکت">
                <Form layout="vertical">
                    <Form.Item label="مسیر مبدأ (from)" required help={form.errors.from_path}>
                        <Input dir="ltr" placeholder="/old-page" value={form.data.from_path} onChange={(e) => form.setData('from_path', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="مسیر مقصد (to)" required help={form.errors.to_path}>
                        <Input dir="ltr" placeholder="/products/erp" value={form.data.to_path} onChange={(e) => form.setData('to_path', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="کد وضعیت">
                        <Select
                            value={form.data.status_code}
                            options={[
                                { label: '۳۰۱ (دائمی)', value: 301 },
                                { label: '۳۰۲ (موقت)', value: 302 },
                            ]}
                            onChange={(v) => form.setData('status_code', v)}
                        />
                    </Form.Item>
                    <Form.Item label="فعال" valuePropName="checked">
                        <Switch checked={form.data.is_active} onChange={(v) => form.setData('is_active', v)} />
                    </Form.Item>
                </Form>
            </Modal>
        </AdminLayout>
    );
}
