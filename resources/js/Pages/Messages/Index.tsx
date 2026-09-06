import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Tag, Button, Drawer, Descriptions, Select, Input, Space, Popconfirm } from 'antd';
import { MailOutlined, EyeOutlined, DeleteOutlined } from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import DataTable from '@/Components/DataTable';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Message {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    company: string | null;
    subject: string | null;
    message: string;
    status: string;
    source_path: string | null;
    admin_notes: string | null;
    created_at: string;
}

type Props = PageProps<{
    rows: Paginated<Message>;
    filters: Record<string, string | undefined>;
    counts: Record<string, number>;
}>;

const STATUS = {
    new: { label: 'جدید', color: 'blue' },
    read: { label: 'خوانده‌شده', color: 'cyan' },
    answered: { label: 'پاسخ‌داده‌شده', color: 'green' },
    closed: { label: 'بسته‌شده', color: 'default' },
} as const;

export default function MessagesIndex({ rows, filters, counts }: Props) {
    const [open, setOpen] = useState<Message | null>(null);
    const [notes, setNotes] = useState('');

    const view = (m: Message) => {
        setOpen(m);
        setNotes(m.admin_notes ?? '');
        if (m.status === 'new') router.get(route('admin.messages.show', m.id), {}, { preserveScroll: true, preserveState: true });
    };

    const setStatus = (m: Message, status: string) =>
        router.put(route('admin.messages.update', m.id), { status, admin_notes: notes }, { preserveScroll: true });

    return (
        <AdminLayout>
            <Head title="پیام‌های تماس" />
            <PageHeader
                title="پیام‌های تماس"
                subtitle={`جدید: ${counts.new ?? 0} • پاسخ‌داده‌شده: ${counts.answered ?? 0}`}
                icon={<MailOutlined />}
            />

            <DataTable<Message>
                routeName="admin.messages.index"
                rows={rows}
                filters={filters}
                searchPlaceholder="جستجوی نام، ایمیل، شرکت"
                filterDefs={[
                    {
                        key: 'status',
                        placeholder: 'وضعیت',
                        options: Object.entries(STATUS).map(([v, s]) => ({ label: s.label, value: v })),
                    },
                ]}
                columns={[
                    { title: 'فرستنده', dataIndex: 'name' },
                    { title: 'شرکت', dataIndex: 'company', render: (v) => v || '—' },
                    { title: 'موضوع', dataIndex: 'subject', render: (v) => v || '—' },
                    {
                        title: 'وضعیت',
                        dataIndex: 'status',
                        width: 120,
                        render: (s: keyof typeof STATUS) => <Tag color={STATUS[s]?.color}>{STATUS[s]?.label ?? s}</Tag>,
                    },
                    {
                        title: 'عملیات',
                        width: 110,
                        render: (_, m) => (
                            <Space size={2}>
                                <Button type="text" icon={<EyeOutlined />} onClick={() => view(m)} />
                                <Popconfirm
                                    title="حذف پیام؟"
                                    okText="حذف"
                                    cancelText="انصراف"
                                    onConfirm={() => router.delete(route('admin.messages.destroy', m.id))}
                                >
                                    <Button type="text" danger icon={<DeleteOutlined />} />
                                </Popconfirm>
                            </Space>
                        ),
                    },
                ]}
            />

            <Drawer open={Boolean(open)} onClose={() => setOpen(null)} width={520} title="جزئیات پیام">
                {open && (
                    <>
                        <Descriptions column={1} bordered size="small">
                            <Descriptions.Item label="نام">{open.name}</Descriptions.Item>
                            <Descriptions.Item label="تلفن">{open.phone || '—'}</Descriptions.Item>
                            <Descriptions.Item label="ایمیل">{open.email || '—'}</Descriptions.Item>
                            <Descriptions.Item label="شرکت">{open.company || '—'}</Descriptions.Item>
                            <Descriptions.Item label="موضوع">{open.subject || '—'}</Descriptions.Item>
                            <Descriptions.Item label="صفحه">{open.source_path || '—'}</Descriptions.Item>
                            <Descriptions.Item label="متن پیام">
                                <div style={{ whiteSpace: 'pre-wrap' }}>{open.message}</div>
                            </Descriptions.Item>
                        </Descriptions>

                        <div style={{ marginTop: 16 }}>
                            <Input.TextArea
                                rows={3}
                                placeholder="یادداشت داخلی…"
                                value={notes}
                                onChange={(e) => setNotes(e.target.value)}
                            />
                        </div>

                        <Space style={{ marginTop: 16 }} wrap>
                            {Object.entries(STATUS).map(([v, s]) => (
                                <Button
                                    key={v}
                                    type={open.status === v ? 'primary' : 'default'}
                                    onClick={() => setStatus(open, v)}
                                >
                                    {s.label}
                                </Button>
                            ))}
                        </Space>
                    </>
                )}
            </Drawer>
        </AdminLayout>
    );
}
