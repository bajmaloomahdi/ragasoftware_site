import { Rate } from 'antd';
import { CommentOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Testimonial {
    id: number;
    customer_id: number | null;
    customer: { name: string } | null;
    author_name: string;
    author_title: string | null;
    body: string;
    rating: number | null;
    avatar_media_id: number | null;
    is_featured: boolean;
    is_active: boolean;
    sort_order: number;
}

type Props = PageProps<{
    rows: Paginated<Testimonial>;
    filters: Record<string, string | undefined>;
    customers: { id: number; name: string }[];
}>;

export default function TestimonialsIndex({ rows, filters, customers }: Props) {
    return (
        <CrudPage<Testimonial>
            title="نظرات مشتریان"
            icon={<CommentOutlined />}
            routeKey="testimonials"
            rows={rows}
            filters={filters}
            width={520}
            columns={[
                { title: 'گوینده', dataIndex: 'author_name' },
                { title: 'سمت', dataIndex: 'author_title', render: (v) => v || '—' },
                { title: 'مشتری', dataIndex: ['customer', 'name'], render: (v) => v || '—' },
                { title: 'امتیاز', dataIndex: 'rating', render: (v: number | null) => (v ? <Rate disabled value={v} style={{ fontSize: 12 }} /> : '—') },
            ]}
            fields={[
                { key: 'author_name', type: 'text', label: 'نام گوینده', required: true },
                { key: 'author_title', type: 'text', label: 'سمت / شرکت' },
                {
                    key: 'customer_id',
                    type: 'select',
                    label: 'مشتری مرتبط',
                    options: customers.map((c) => ({ label: c.name, value: c.id })),
                },
                { key: 'body', type: 'textarea', label: 'متن نظر', required: true, rows: 5 },
                { key: 'rating', type: 'number', label: 'امتیاز (۱ تا ۵)' },
                { key: 'avatar_media_id', type: 'media', label: 'تصویر گوینده' },
                { key: 'is_featured', type: 'switch', label: 'ویژه' },
                { key: 'is_active', type: 'switch', label: 'فعال' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({
                author_name: r?.author_name ?? '',
                author_title: r?.author_title ?? '',
                customer_id: r?.customer_id ?? null,
                body: r?.body ?? '',
                rating: r?.rating ?? null,
                avatar_media_id: r?.avatar_media_id ?? null,
                is_featured: r?.is_featured ?? false,
                is_active: r?.is_active ?? true,
                sort_order: r?.sort_order ?? 0,
            })}
        />
    );
}
