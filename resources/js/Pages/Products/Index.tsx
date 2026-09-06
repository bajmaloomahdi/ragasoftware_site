import { Image, Tag } from 'antd';
import { ShopOutlined } from '@ant-design/icons';
import ContentListPage, { statusColumn } from '@/Components/ContentListPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Product {
    id: number;
    title: string;
    tagline: string | null;
    status: string;
    featured: boolean;
    sort_order: number;
    category: { name: string } | null;
    hero_image: { thumb_url: string } | null;
}

type Props = PageProps<{
    rows: Paginated<Product>;
    filters: Record<string, string | undefined>;
    categories: { id: number; name: string }[];
}>;

export default function ProductsIndex({ rows, filters, categories }: Props) {
    return (
        <ContentListPage<Product>
            title="محصولات"
            subtitle="محصولات و راهکارهای نرم‌افزاری"
            icon={<ShopOutlined />}
            routeKey="products"
            rows={rows}
            filters={filters}
            searchPlaceholder="جستجوی عنوان محصول"
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
                {
                    title: '',
                    dataIndex: ['hero_image', 'thumb_url'],
                    width: 56,
                    render: (v: string) => (v ? <Image src={v} width={40} height={40} style={{ objectFit: 'cover' }} /> : '—'),
                },
                { title: 'عنوان', dataIndex: 'title', sorter: true },
                { title: 'دسته', dataIndex: ['category', 'name'], render: (v) => v || '—' },
                statusColumn<Product>(),
                { title: 'ویژه', dataIndex: 'featured', width: 70, render: (v: boolean) => (v ? <Tag color="blue">ویژه</Tag> : '') },
                { title: 'ترتیب', dataIndex: 'sort_order', width: 70, sorter: true },
            ]}
        />
    );
}
