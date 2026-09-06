import { TagsOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Category {
    id: number;
    name: string;
    slug: string;
    sort_order: number;
    faqs_count?: number;
    faqs?: unknown[];
}

type Props = PageProps<{ rows: Paginated<Category>; filters: Record<string, string | undefined> }>;

export default function FaqCategories({ rows, filters }: Props) {
    return (
        <CrudPage<Category>
            title="دسته‌بندی سؤالات متداول"
            icon={<TagsOutlined />}
            routeKey="faq-categories"
            rows={rows}
            filters={filters}
            columns={[
                { title: 'نام', dataIndex: 'name', sorter: true },
                { title: 'اسلاگ', dataIndex: 'slug' },
                { title: 'تعداد سؤال', render: (_, r) => r.faqs?.length ?? 0 },
            ]}
            fields={[
                { key: 'name', type: 'text', label: 'نام دسته', required: true },
                { key: 'slug', type: 'slug', label: 'اسلاگ (اختیاری)' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({ name: r?.name ?? '', slug: r?.slug ?? '', sort_order: r?.sort_order ?? 0 })}
        />
    );
}
