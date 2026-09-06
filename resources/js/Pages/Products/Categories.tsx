import { TagsOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    icon: string | null;
    sort_order: number;
    products?: unknown[];
}

type Props = PageProps<{ rows: Paginated<Category>; filters: Record<string, string | undefined> }>;

export default function ProductCategories({ rows, filters }: Props) {
    return (
        <CrudPage<Category>
            title="دسته‌بندی محصولات"
            icon={<TagsOutlined />}
            routeKey="product-categories"
            rows={rows}
            filters={filters}
            columns={[
                { title: 'نام', dataIndex: 'name', sorter: true },
                { title: 'اسلاگ', dataIndex: 'slug' },
                { title: 'تعداد محصول', render: (_, r) => r.products?.length ?? 0 },
            ]}
            fields={[
                { key: 'name', type: 'text', label: 'نام دسته', required: true },
                { key: 'slug', type: 'slug', label: 'اسلاگ (اختیاری)' },
                { key: 'description', type: 'textarea', label: 'توضیح' },
                { key: 'icon', type: 'text', label: 'نام آیکن (اختیاری)' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({
                name: r?.name ?? '',
                slug: r?.slug ?? '',
                description: r?.description ?? '',
                icon: r?.icon ?? '',
                sort_order: r?.sort_order ?? 0,
            })}
        />
    );
}
