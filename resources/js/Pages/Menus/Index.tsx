import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import {
    Button, Card, Input, Select, Space, Table, Tabs, Modal, Form, Popconfirm, App, Switch,
} from 'antd';
import { MenuOutlined, PlusOutlined, DeleteOutlined, SaveOutlined } from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import type { PageProps } from '@/types/global';

interface Item {
    id?: number | null;
    label: string;
    link_type: string;
    link_value: string | null;
    target: string;
    icon: string | null;
    is_active: boolean;
}
interface Menu {
    id: number;
    name: string;
    location: string;
    items: Item[];
}

type Props = PageProps<{
    menus: Menu[];
    linkTargets: {
        pages: { id: number; title: string; slug: string }[];
        products: { id: number; title: string; slug: string }[];
        services: { id: number; title: string; slug: string }[];
    };
    locations: Record<string, string>;
}>;

const LINK_TYPES = [
    { label: 'نشانی دلخواه', value: 'url' },
    { label: 'صفحه', value: 'page' },
    { label: 'محصول', value: 'product' },
    { label: 'خدمت', value: 'service' },
    { label: 'وبلاگ', value: 'blog_index' },
];

export default function MenusIndex({ menus, linkTargets, locations }: Props) {
    const { message } = App.useApp();
    const [drafts, setDrafts] = useState<Record<number, Item[]>>(
        Object.fromEntries(menus.map((m) => [m.id, m.items])),
    );
    const [newMenuOpen, setNewMenuOpen] = useState(false);
    const [newMenu, setNewMenu] = useState({ name: '', location: '' });

    const usedLocations = menus.map((m) => m.location);
    const freeLocations = Object.entries(locations).filter(([k]) => !usedLocations.includes(k));

    const setItems = (menuId: number, items: Item[]) => setDrafts((d) => ({ ...d, [menuId]: items }));

    const patchItem = (menuId: number, i: number, patch: Partial<Item>) =>
        setItems(menuId, drafts[menuId].map((it, idx) => (idx === i ? { ...it, ...patch } : it)));

    const targetOptions = (type: string) => {
        const list =
            type === 'page' ? linkTargets.pages : type === 'product' ? linkTargets.products : type === 'service' ? linkTargets.services : [];
        return list.map((x) => ({ label: x.title, value: x.slug }));
    };

    const save = (menu: Menu) => {
        router.put(
            route('admin.menus.sync', menu.id),
            { items: drafts[menu.id].map((it) => ({ ...it, parent_key: null })) },
            { preserveScroll: true, onSuccess: () => message.success('ذخیره شد') },
        );
    };

    return (
        <AdminLayout>
            <Head title="منوها" />
            <PageHeader
                title="منوها"
                subtitle="منوی هدر، فوتر و موبایل — کاملاً پویا"
                icon={<MenuOutlined />}
                actions={
                    freeLocations.length > 0 && (
                        <Button type="primary" icon={<PlusOutlined />} onClick={() => setNewMenuOpen(true)}>
                            منوی جدید
                        </Button>
                    )
                }
            />

            {menus.length === 0 ? (
                <Card>هنوز منویی ساخته نشده است.</Card>
            ) : (
                <Tabs
                    items={menus.map((menu) => ({
                        key: String(menu.id),
                        label: locations[menu.location] ?? menu.name,
                        children: (
                            <Card
                                extra={
                                    <Space>
                                        <Button type="primary" icon={<SaveOutlined />} onClick={() => save(menu)}>
                                            ذخیره منو
                                        </Button>
                                        <Popconfirm
                                            title="حذف کل منو؟"
                                            okText="حذف"
                                            cancelText="انصراف"
                                            onConfirm={() => router.delete(route('admin.menus.destroy', menu.id))}
                                        >
                                            <Button danger icon={<DeleteOutlined />} />
                                        </Popconfirm>
                                    </Space>
                                }
                            >
                                <Table
                                    rowKey={(_, i) => String(i)}
                                    pagination={false}
                                    dataSource={drafts[menu.id]}
                                    locale={{ emptyText: 'آیتمی نیست' }}
                                    columns={[
                                        {
                                            title: 'عنوان',
                                            render: (_, __, i) => (
                                                <Input
                                                    value={drafts[menu.id][i].label}
                                                    onChange={(e) => patchItem(menu.id, i, { label: e.target.value })}
                                                />
                                            ),
                                        },
                                        {
                                            title: 'نوع',
                                            width: 150,
                                            render: (_, __, i) => (
                                                <Select
                                                    style={{ width: '100%' }}
                                                    options={LINK_TYPES}
                                                    value={drafts[menu.id][i].link_type}
                                                    onChange={(v) => patchItem(menu.id, i, { link_type: v, link_value: '' })}
                                                />
                                            ),
                                        },
                                        {
                                            title: 'مقصد',
                                            render: (_, __, i) => {
                                                const it = drafts[menu.id][i];
                                                if (it.link_type === 'url' || it.link_type === 'blog_index') {
                                                    return (
                                                        <Input
                                                            dir="ltr"
                                                            placeholder={it.link_type === 'blog_index' ? '/blog' : 'https://…'}
                                                            value={it.link_value ?? ''}
                                                            onChange={(e) => patchItem(menu.id, i, { link_value: e.target.value })}
                                                        />
                                                    );
                                                }
                                                return (
                                                    <Select
                                                        style={{ width: '100%' }}
                                                        showSearch
                                                        optionFilterProp="label"
                                                        options={targetOptions(it.link_type)}
                                                        value={it.link_value ?? undefined}
                                                        onChange={(v) => patchItem(menu.id, i, { link_value: v })}
                                                    />
                                                );
                                            },
                                        },
                                        {
                                            title: 'فعال',
                                            width: 70,
                                            render: (_, __, i) => (
                                                <Switch
                                                    checked={drafts[menu.id][i].is_active}
                                                    onChange={(v) => patchItem(menu.id, i, { is_active: v })}
                                                />
                                            ),
                                        },
                                        {
                                            title: '',
                                            width: 90,
                                            render: (_, __, i) => (
                                                <Space>
                                                    <Button size="small" disabled={i === 0} onClick={() => {
                                                        const arr = [...drafts[menu.id]];
                                                        [arr[i - 1], arr[i]] = [arr[i], arr[i - 1]];
                                                        setItems(menu.id, arr);
                                                    }}>↑</Button>
                                                    <Button
                                                        size="small"
                                                        danger
                                                        icon={<DeleteOutlined />}
                                                        onClick={() => setItems(menu.id, drafts[menu.id].filter((_, idx) => idx !== i))}
                                                    />
                                                </Space>
                                            ),
                                        },
                                    ]}
                                />
                                <Button
                                    type="dashed"
                                    block
                                    icon={<PlusOutlined />}
                                    style={{ marginTop: 12 }}
                                    onClick={() =>
                                        setItems(menu.id, [
                                            ...drafts[menu.id],
                                            { label: 'مورد جدید', link_type: 'url', link_value: '', target: '_self', icon: null, is_active: true },
                                        ])
                                    }
                                >
                                    افزودن آیتم
                                </Button>
                            </Card>
                        ),
                    }))}
                />
            )}

            <Modal
                open={newMenuOpen}
                title="منوی جدید"
                okText="ساخت"
                cancelText="انصراف"
                onCancel={() => setNewMenuOpen(false)}
                onOk={() =>
                    router.post(route('admin.menus.store'), newMenu, {
                        onSuccess: () => {
                            setNewMenuOpen(false);
                            setNewMenu({ name: '', location: '' });
                        },
                    })
                }
            >
                <Form layout="vertical">
                    <Form.Item label="نام منو" required>
                        <Input value={newMenu.name} onChange={(e) => setNewMenu({ ...newMenu, name: e.target.value })} />
                    </Form.Item>
                    <Form.Item label="جایگاه" required>
                        <Select
                            options={freeLocations.map(([k, v]) => ({ label: v, value: k }))}
                            value={newMenu.location || undefined}
                            onChange={(v) => setNewMenu({ ...newMenu, location: v })}
                        />
                    </Form.Item>
                </Form>
            </Modal>
        </AdminLayout>
    );
}
