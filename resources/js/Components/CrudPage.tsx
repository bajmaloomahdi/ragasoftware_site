import { useState, type ReactNode } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Button, Drawer, Form, Space, Popconfirm } from 'antd';
import { PlusOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import type { TableProps } from 'antd';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import DataTable from '@/Components/DataTable';
import FieldRenderer, { type FieldSpec } from '@/Components/FieldRenderer';
import type { Paginated } from '@/types/models';

interface Props<T> {
    title: string;
    subtitle?: string;
    icon?: ReactNode;
    routeKey: string; // e.g. "customers" → admin.customers.{index,store,update,destroy}
    rows: Paginated<T>;
    filters?: Record<string, string | undefined>;
    columns: TableProps<T>['columns'];
    fields: FieldSpec[];
    /** map a record → form values for editing */
    toForm: (record: T | null) => Record<string, unknown>;
    filterDefs?: React.ComponentProps<typeof DataTable>['filterDefs'];
    searchPlaceholder?: string;
    width?: number;
    extraRowActions?: (record: T) => ReactNode;
}

/**
 * One-file CRUD screen for the simpler CMS entities: list + slide-over form
 * generated from a field schema. Wraps DataTable + Inertia useForm.
 */
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function CrudPage<T extends { id: number } & Record<string, any>>({
    title, subtitle, icon, routeKey, rows, filters = {}, columns, fields, toForm,
    filterDefs, searchPlaceholder, width = 460, extraRowActions,
}: Props<T>) {
    const [open, setOpen] = useState(false);
    const [editing, setEditing] = useState<T | null>(null);
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>(toForm(null));

    const startCreate = () => {
        setEditing(null);
        form.setData(toForm(null));
        form.clearErrors();
        setOpen(true);
    };
    const startEdit = (record: T) => {
        setEditing(record);
        form.setData(toForm(record));
        form.clearErrors();
        setOpen(true);
    };

    const submit = () => {
        const opts = { onSuccess: () => setOpen(false), preserveScroll: true };
        if (editing) form.put(route(`admin.${routeKey}.update`, editing.id), opts);
        else form.post(route(`admin.${routeKey}.store`), opts);
    };

    const visibleFields = fields.filter((f) => {
        if (!f.when) return true;
        const [k, v] = f.when;
        return form.data[k] === v;
    });

    const actionColumn: TableProps<T>['columns'] = [
        {
            title: 'عملیات',
            key: '_actions',
            fixed: 'right',
            width: 130,
            render: (_: unknown, record: T) => (
                <Space size={2}>
                    {extraRowActions?.(record)}
                    <Button type="text" icon={<EditOutlined />} onClick={() => startEdit(record)} />
                    <Popconfirm
                        title="حذف این مورد؟"
                        okText="حذف"
                        cancelText="انصراف"
                        onConfirm={() =>
                            router.delete(route(`admin.${routeKey}.destroy`, record.id), { preserveScroll: true })
                        }
                    >
                        <Button type="text" danger icon={<DeleteOutlined />} />
                    </Popconfirm>
                </Space>
            ),
        },
    ];

    return (
        <AdminLayout>
            <Head title={title} />
            <PageHeader
                title={title}
                subtitle={subtitle}
                icon={icon}
                actions={
                    <Button type="primary" icon={<PlusOutlined />} onClick={startCreate}>
                        افزودن
                    </Button>
                }
            />

            <DataTable
                routeName={`admin.${routeKey}.index`}
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                rows={rows as any}
                filters={filters}
                // eslint-disable-next-line @typescript-eslint/no-explicit-any
                columns={[...(columns ?? []), ...actionColumn] as any}
                filterDefs={filterDefs}
                searchPlaceholder={searchPlaceholder}
            />

            <Drawer
                open={open}
                onClose={() => setOpen(false)}
                width={width}
                title={editing ? 'ویرایش' : 'افزودن مورد جدید'}
                extra={
                    <Button type="primary" loading={form.processing} onClick={submit}>
                        ذخیره
                    </Button>
                }
            >
                <Form layout="vertical">
                    {visibleFields.map((f) => (
                        <FieldRenderer
                            key={f.key}
                            field={f}
                            value={form.data[f.key]}
                            error={form.errors[f.key as keyof typeof form.errors] as string | undefined}
                            onChange={(v) => form.setData(f.key, v)}
                        />
                    ))}
                </Form>
            </Drawer>
        </AdminLayout>
    );
}
