import { ToolOutlined } from '@ant-design/icons';
import ContentListPage, { statusColumn } from '@/Components/ContentListPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Service {
    id: number;
    title: string;
    summary: string | null;
    status: string;
    featured: boolean;
    sort_order: number;
}

type Props = PageProps<{ rows: Paginated<Service>; filters: Record<string, string | undefined> }>;

export default function ServicesIndex({ rows, filters }: Props) {
    return (
        <ContentListPage<Service>
            title="خدمات"
            subtitle="خدمات و راهکارهای شرکت"
            icon={<ToolOutlined />}
            routeKey="services"
            rows={rows}
            filters={filters}
            searchPlaceholder="جستجوی عنوان خدمت"
            filterDefs={[
                {
                    key: 'status',
                    placeholder: 'وضعیت',
                    options: [
                        { label: 'پیش‌نویس', value: 'draft' },
                        { label: 'زمان‌بندی', value: 'scheduled' },
                        { label: 'منتشرشده', value: 'published' },
                    ],
                },
            ]}
            columns={[
                { title: 'عنوان', dataIndex: 'title', sorter: true },
                { title: 'خلاصه', dataIndex: 'summary', render: (v) => v || '—', ellipsis: true },
                statusColumn<Service>(),
                { title: 'ترتیب', dataIndex: 'sort_order', width: 70, sorter: true },
            ]}
        />
    );
}
