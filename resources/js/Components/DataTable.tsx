import { useMemo, useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import { Table, Input, Select, Space, Button, Card } from 'antd';
import type { TableProps } from 'antd';
import { ReloadOutlined } from '@ant-design/icons';
import type { Paginated } from '@/types/models';

interface FilterDef {
    key: string;
    placeholder: string;
    options: { label: string; value: string }[];
    width?: number;
}

interface Props<T> {
    routeName: string;
    routeParams?: Record<string, unknown>;
    rows: Paginated<T>;
    columns: TableProps<T>['columns'];
    filters?: Record<string, string | undefined>;
    searchPlaceholder?: string;
    filterDefs?: FilterDef[];
    rowKey?: string;
    onRow?: TableProps<T>['onRow'];
}

/**
 * Inertia-backed table: debounced search, dropdown filters, server sort and
 * pagination — all pushed to the URL with preserveState. Used by every CMS
 * list screen so behaviour stays identical everywhere.
 */
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function DataTable<T extends Record<string, any> = Record<string, unknown>>({
    routeName,
    routeParams = {},
    rows,
    columns,
    filters = {},
    searchPlaceholder = 'جستجو…',
    filterDefs = [],
    rowKey = 'id',
    onRow,
}: Props<T>) {
    const [search, setSearch] = useState(filters.search ?? '');
    const timer = useRef<number | undefined>(undefined);

    const push = (patch: Record<string, unknown>) => {
        router.get(
            route(routeName, routeParams),
            { ...filters, ...patch } as Record<string, string>,
            { preserveState: true, preserveScroll: true, replace: true },
        );
    };

    const onSearch = (value: string) => {
        setSearch(value);
        window.clearTimeout(timer.current);
        timer.current = window.setTimeout(() => push({ search: value || undefined, page: undefined }), 300);
    };

    const pagination = useMemo(
        () => ({
            current: rows.current_page,
            pageSize: rows.per_page,
            total: rows.total,
            showSizeChanger: false,
            showTotal: (t: number) => `${t} مورد`,
        }),
        [rows],
    );

    const handleChange: TableProps<T>['onChange'] = (page, _f, sorter) => {
        const s = Array.isArray(sorter) ? sorter[0] : sorter;
        push({
            page: page.current,
            sort: s?.order ? (s.field as string) : undefined,
            direction: s?.order === 'descend' ? 'desc' : s?.order === 'ascend' ? 'asc' : undefined,
        });
    };

    return (
        <Card styles={{ body: { padding: 16 } }}>
            <Space wrap style={{ marginBottom: 16 }}>
                <Input.Search
                    allowClear
                    placeholder={searchPlaceholder}
                    value={search}
                    style={{ width: 260 }}
                    onChange={(e) => onSearch(e.target.value)}
                />
                {filterDefs.map((f) => (
                    <Select
                        key={f.key}
                        allowClear
                        placeholder={f.placeholder}
                        style={{ width: f.width ?? 160 }}
                        value={filters[f.key] || undefined}
                        options={f.options}
                        onChange={(v) => push({ [f.key]: v || undefined, page: undefined })}
                    />
                ))}
                {(search || filterDefs.some((f) => filters[f.key])) && (
                    <Button
                        icon={<ReloadOutlined />}
                        onClick={() => {
                            setSearch('');
                            router.get(route(routeName, routeParams), {}, { preserveState: true, replace: true });
                        }}
                    >
                        پاک‌کردن
                    </Button>
                )}
            </Space>

            <Table<T>
                rowKey={rowKey}
                columns={columns}
                dataSource={rows.data}
                pagination={pagination}
                onChange={handleChange}
                onRow={onRow}
                scroll={{ x: 'max-content' }}
                size="middle"
            />
        </Card>
    );
}
