import { TagsOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Category {
    id: number;
    name: string;
    slug: string;
    parent_id: number | null;
    description: string | null;
    sort_order: number;
    posts?: unknown[];
}

type Props = PageProps<{ rows: Paginated<Category>; filters: Record<string, string | undefined> }>;

export default function BlogCategories({ rows, filters }: Props) {
    return (
        <CrudPage<Category>
            title="دسته‌بندی مقالات"
            icon={<TagsOutlined />}
            routeKey="blog-categories"
            rows={rows}
            filters={filters}
            columns={[
                { title: 'نام', dataIndex: 'name', sorter: true },
                { title: 'اسلاگ', dataIndex: 'slug' },
                { title: 'تعداد مقاله', render: (_, r) => r.posts?.length ?? 0 },
            ]}
            fields={[
                { key: 'name', type: 'text', label: 'نام دسته', required: true },
                { key: 'slug', type: 'slug', label: 'اسلاگ (اختیاری)' },
                {
                    key: 'parent_id',
                    type: 'select',
                    label: 'دستهٔ والد',
                    options: rows.data.map((c) => ({ label: c.name, value: c.id })),
                },
                { key: 'description', type: 'textarea', label: 'توضیح' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({
                name: r?.name ?? '',
                slug: r?.slug ?? '',
                parent_id: r?.parent_id ?? null,
                description: r?.description ?? '',
                sort_order: r?.sort_order ?? 0,
            })}
        />
    );
}
