import type { ReactNode } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Button, Space, Popconfirm, Tag } from 'antd';
import { PlusOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import type { TableProps } from 'antd';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import DataTable from '@/Components/DataTable';
import type { Paginated } from '@/types/models';

const STATUS_LABEL: Record<string, string> = {
    draft: 'پیش‌نویس',
    scheduled: 'زمان‌بندی',
    published: 'منتشرشده',
};
const STATUS_COLOR: Record<string, string> = { draft: 'default', scheduled: 'gold', published: 'green' };

export function statusColumn<T>(): NonNullable<TableProps<T>['columns']>[number] {
    return {
        title: 'وضعیت',
        dataIndex: 'status',
        width: 110,
        render: (s: string) => <Tag color={STATUS_COLOR[s]}>{STATUS_LABEL[s] ?? s}</Tag>,
    };
}

interface Props<T> {
    title: string;
    subtitle?: string;
    icon?: ReactNode;
    routeKey: string; // "products" → admin.products.{index,create,edit,destroy}
    rows: Paginated<T>;
    filters?: Record<string, string | undefined>;
    columns: TableProps<T>['columns'];
    filterDefs?: React.ComponentProps<typeof DataTable>['filterDefs'];
    searchPlaceholder?: string;
    canCreate?: boolean;
}

/** List screen for entities edited on their own page (products, services, …). */
export default function ContentListPage<T extends { id: number }>({
    title, subtitle, icon, routeKey, rows, filters = {}, columns, filterDefs, searchPlaceholder, canCreate = true,
}: Props<T>) {
    const withActions: TableProps<T>['columns'] = [
        ...(columns ?? []),
        {
            title: 'عملیات',
            key: '_a',
            fixed: 'right',
            width: 110,
            render: (_: unknown, r: T) => (
                <Space size={2}>
                    <Link href={route(`admin.${routeKey}.edit`, r.id)}>
                        <Button type="text" icon={<EditOutlined />} />
                    </Link>
                    <Popconfirm
                        title="حذف این مورد؟"
                        okText="حذف"
                        cancelText="انصراف"
                        onConfirm={() => router.delete(route(`admin.${routeKey}.destroy`, r.id))}
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
                    canCreate && (
                        <Link href={route(`admin.${routeKey}.create`)}>
                            <Button type="primary" icon={<PlusOutlined />}>
                                افزودن
                            </Button>
                        </Link>
                    )
                }
            />
            <DataTable
                routeName={`admin.${routeKey}.index`}
                rows={rows as never}
                filters={filters}
                columns={withActions as never}
                filterDefs={filterDefs}
                searchPlaceholder={searchPlaceholder}
            />
        </AdminLayout>
    );
}
