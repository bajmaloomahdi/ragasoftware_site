import { Tag } from 'antd';
import { ReadOutlined } from '@ant-design/icons';
import ContentListPage, { statusColumn } from '@/Components/ContentListPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Post {
    id: number;
    title: string;
    status: string;
    published_at: string | null;
    views: number;
    is_featured: boolean;
    category: { name: string } | null;
    author: { name: string } | null;
}

type Props = PageProps<{
    rows: Paginated<Post>;
    filters: Record<string, string | undefined>;
    categories: { id: number; name: string }[];
}>;

export default function BlogIndex({ rows, filters, categories }: Props) {
    return (
        <ContentListPage<Post>
            title="مقالات"
            subtitle="مدیریت مقالات وبلاگ"
            icon={<ReadOutlined />}
            routeKey="posts"
            rows={rows}
            filters={filters}
            searchPlaceholder="جستجوی عنوان مقاله"
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
                {
                    key: 'category',
                    placeholder: 'دسته',
                    options: categories.map((c) => ({ label: c.name, value: String(c.id) })),
                },
            ]}
            columns={[
                { title: 'عنوان', dataIndex: 'title', sorter: true },
                { title: 'دسته', dataIndex: ['category', 'name'], render: (v) => v || '—' },
                { title: 'نویسنده', dataIndex: ['author', 'name'], render: (v) => v || '—' },
                statusColumn<Post>(),
                { title: 'بازدید', dataIndex: 'views', width: 80, sorter: true },
                { title: 'ویژه', dataIndex: 'is_featured', width: 70, render: (v: boolean) => (v ? <Tag color="blue">ویژه</Tag> : '') },
            ]}
        />
    );
}
