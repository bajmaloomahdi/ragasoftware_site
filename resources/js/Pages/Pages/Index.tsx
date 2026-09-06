import { Tag, Space } from 'antd';
import { FileTextOutlined, HomeOutlined } from '@ant-design/icons';
import ContentListPage, { statusColumn } from '@/Components/ContentListPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Page {
    id: number;
    title: string;
    slug: string;
    template: string;
    status: string;
    is_homepage: boolean;
    show_in_sitemap: boolean;
}

type Props = PageProps<{ rows: Paginated<Page>; filters: Record<string, string | undefined> }>;

export default function PagesIndex({ rows, filters }: Props) {
    return (
        <ContentListPage<Page>
            title="صفحات"
            subtitle="صفحات ثابت و صفحات پویا با بخش‌بندی (Section Builder)"
            icon={<FileTextOutlined />}
            routeKey="pages"
            rows={rows}
            filters={filters}
            searchPlaceholder="جستجوی عنوان یا اسلاگ"
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
                    title: 'عنوان',
                    dataIndex: 'title',
                    render: (v, r) => (
                        <Space>
                            {r.is_homepage && <HomeOutlined style={{ color: '#2563eb' }} />}
                            {v}
                        </Space>
                    ),
                },
                { title: 'اسلاگ', dataIndex: 'slug', render: (v, r) => (r.is_homepage ? '/' : `/${v}`) },
                { title: 'قالب', dataIndex: 'template', width: 110 },
                statusColumn<Page>(),
                {
                    title: 'Sitemap',
                    dataIndex: 'show_in_sitemap',
                    width: 90,
                    render: (v: boolean) => <Tag color={v ? 'green' : 'default'}>{v ? 'بله' : 'خیر'}</Tag>,
                },
            ]}
        />
    );
}
