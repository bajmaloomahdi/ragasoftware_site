import { Image } from 'antd';
import { ProjectOutlined } from '@ant-design/icons';
import ContentListPage, { statusColumn } from '@/Components/ContentListPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Project {
    id: number;
    title: string;
    client_name: string | null;
    status: string;
    completed_on: string | null;
    sort_order: number;
    cover: { thumb_url: string } | null;
    customer: { name: string } | null;
}

type Props = PageProps<{ rows: Paginated<Project>; filters: Record<string, string | undefined> }>;

export default function ProjectsIndex({ rows, filters }: Props) {
    return (
        <ContentListPage<Project>
            title="نمونه‌کارها"
            subtitle="پروژه‌های اجراشده"
            icon={<ProjectOutlined />}
            routeKey="projects"
            rows={rows}
            filters={filters}
            searchPlaceholder="جستجوی عنوان یا کارفرما"
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
                {
                    title: '',
                    dataIndex: ['cover', 'thumb_url'],
                    width: 56,
                    render: (v: string) => (v ? <Image src={v} width={40} height={40} style={{ objectFit: 'cover' }} /> : '—'),
                },
                { title: 'عنوان', dataIndex: 'title', sorter: true },
                { title: 'کارفرما', dataIndex: 'client_name', render: (v, r) => v || r.customer?.name || '—' },
                statusColumn<Project>(),
                { title: 'ترتیب', dataIndex: 'sort_order', width: 70, sorter: true },
            ]}
        />
    );
}
