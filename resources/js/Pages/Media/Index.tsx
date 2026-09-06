import { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import {
    Button, Card, Image, Input, Modal, Popconfirm, Segmented, Space, Form, App, Empty, Pagination, Tag,
} from 'antd';
import { PictureOutlined, UploadOutlined, DeleteOutlined, EditOutlined, FolderAddOutlined } from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import http from '@/lib/http';
import type { PageProps } from '@/types/global';
import type { MediaItem, MediaFolderItem, Paginated } from '@/types/models';

type Props = PageProps<{
    media: Paginated<MediaItem>;
    folders: MediaFolderItem[];
    filters: { folder?: string; search?: string; type?: string };
}>;

function bytes(n: number) {
    if (n < 1024) return `${n} B`;
    if (n < 1024 * 1024) return `${(n / 1024).toFixed(0)} KB`;
    return `${(n / 1024 / 1024).toFixed(1)} MB`;
}

export default function MediaIndex({ media, folders, filters }: Props) {
    const { message } = App.useApp();
    const [editing, setEditing] = useState<MediaItem | null>(null);
    const [newFolder, setNewFolder] = useState(false);
    const form = useForm({ alt_text: '', title: '', caption: '' });
    const folderForm = useForm({ name: '', parent_id: null as number | null });

    const setFilter = (patch: Record<string, unknown>) =>
        router.get(route('admin.media.index'), { ...filters, ...patch }, { preserveState: true, replace: true });

    const upload = async (files: FileList | null) => {
        if (!files?.length) return;
        const data = new FormData();
        Array.from(files).forEach((f) => data.append('files[]', f));
        if (filters.folder) data.append('folder_id', filters.folder);
        try {
            await http.post(route('admin.media.store'), data);
            message.success('بارگذاری انجام شد.');
            router.reload({ only: ['media'] });
        } catch {
            message.error('بارگذاری ناموفق بود (نوع یا حجم فایل).');
        }
    };

    const openEdit = (m: MediaItem) => {
        setEditing(m);
        form.setData({ alt_text: m.alt_text ?? '', title: m.title ?? '', caption: m.caption ?? '' });
    };

    const saveEdit = () => {
        if (!editing) return;
        form.put(route('admin.media.update', editing.id), {
            preserveScroll: true,
            onSuccess: () => setEditing(null),
        });
    };

    return (
        <AdminLayout>
            <Head title="کتابخانه رسانه" />
            <PageHeader
                title="کتابخانه رسانه"
                subtitle="مدیریت تصاویر و فایل‌های سایت"
                icon={<PictureOutlined />}
                actions={
                    <Space wrap>
                        <Button icon={<FolderAddOutlined />} onClick={() => setNewFolder(true)}>
                            پوشه جدید
                        </Button>
                        <Button type="primary" icon={<UploadOutlined />}>
                            <label style={{ cursor: 'pointer' }}>
                                بارگذاری
                                <input
                                    type="file"
                                    multiple
                                    hidden
                                    accept="image/*,.pdf"
                                    onChange={(e) => upload(e.target.files)}
                                />
                            </label>
                        </Button>
                    </Space>
                }
            />

            <Card styles={{ body: { padding: 16 } }}>
                <Space wrap style={{ marginBottom: 16 }}>
                    <Input.Search
                        placeholder="جستجوی نام / عنوان / متن جایگزین"
                        defaultValue={filters.search}
                        allowClear
                        style={{ width: 280 }}
                        onSearch={(v) => setFilter({ search: v || undefined })}
                    />
                    <Segmented
                        options={[
                            { label: 'همه', value: '' },
                            ...folders.map((f) => ({ label: f.name, value: String(f.id) })),
                        ]}
                        value={filters.folder ?? ''}
                        onChange={(v) => setFilter({ folder: v || undefined })}
                    />
                </Space>

                {media.data.length === 0 ? (
                    <Empty description="رسانه‌ای وجود ندارد" />
                ) : (
                    <div
                        style={{
                            display: 'grid',
                            gridTemplateColumns: 'repeat(auto-fill, minmax(150px, 1fr))',
                            gap: 14,
                        }}
                    >
                        {media.data.map((m) => (
                            <Card
                                key={m.id}
                                size="small"
                                styles={{ body: { padding: 8 } }}
                                cover={
                                    <div style={{ aspectRatio: '4/3', background: '#f5f7fa', overflow: 'hidden' }}>
                                        <Image
                                            src={m.thumb_url}
                                            style={{ objectFit: 'cover', width: '100%', height: '100%' }}
                                        />
                                    </div>
                                }
                                actions={[
                                    <EditOutlined key="e" onClick={() => openEdit(m)} />,
                                    <Popconfirm
                                        key="d"
                                        title="حذف این فایل؟"
                                        okText="حذف"
                                        cancelText="انصراف"
                                        onConfirm={() =>
                                            router.delete(route('admin.media.destroy', m.id), { preserveScroll: true })
                                        }
                                    >
                                        <DeleteOutlined />
                                    </Popconfirm>,
                                ]}
                            >
                                <Card.Meta
                                    title={
                                        <span style={{ fontSize: 12, fontWeight: 400 }} title={m.original_name}>
                                            {m.original_name}
                                        </span>
                                    }
                                    description={
                                        <Space size={4} wrap>
                                            <Tag>{m.extension}</Tag>
                                            <span style={{ fontSize: 11 }}>{bytes(m.size)}</span>
                                            {!m.alt_text && <Tag color="orange">بدون Alt</Tag>}
                                        </Space>
                                    }
                                />
                            </Card>
                        ))}
                    </div>
                )}

                {media.last_page > 1 && (
                    <div style={{ marginTop: 16, textAlign: 'center' }}>
                        <Pagination
                            current={media.current_page}
                            total={media.total}
                            pageSize={media.per_page}
                            showSizeChanger={false}
                            onChange={(p) => setFilter({ page: p })}
                        />
                    </div>
                )}
            </Card>

            {/* Edit metadata */}
            <Modal
                open={Boolean(editing)}
                onCancel={() => setEditing(null)}
                onOk={saveEdit}
                confirmLoading={form.processing}
                okText="ذخیره"
                cancelText="انصراف"
                title="ویرایش اطلاعات فایل"
            >
                {editing && (
                    <Space align="start" size={16}>
                        <Image src={editing.thumb_url} width={120} />
                        <Form layout="vertical" style={{ flex: 1, minWidth: 260 }}>
                            <Form.Item label="متن جایگزین (Alt)" required>
                                <Input
                                    value={form.data.alt_text}
                                    onChange={(e) => form.setData('alt_text', e.target.value)}
                                />
                            </Form.Item>
                            <Form.Item label="عنوان">
                                <Input value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                            </Form.Item>
                            <Form.Item label="توضیح">
                                <Input.TextArea
                                    rows={2}
                                    value={form.data.caption}
                                    onChange={(e) => form.setData('caption', e.target.value)}
                                />
                            </Form.Item>
                        </Form>
                    </Space>
                )}
            </Modal>

            {/* New folder */}
            <Modal
                open={newFolder}
                onCancel={() => setNewFolder(false)}
                onOk={() =>
                    folderForm.post(route('admin.media-folders.store'), {
                        onSuccess: () => {
                            setNewFolder(false);
                            folderForm.reset();
                        },
                    })
                }
                confirmLoading={folderForm.processing}
                okText="ساخت"
                cancelText="انصراف"
                title="پوشه جدید"
            >
                <Form layout="vertical">
                    <Form.Item label="نام پوشه" required>
                        <Input
                            value={folderForm.data.name}
                            onChange={(e) => folderForm.setData('name', e.target.value)}
                        />
                    </Form.Item>
                </Form>
            </Modal>
        </AdminLayout>
    );
}
