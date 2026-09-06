import { TagsOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Tag {
    id: number;
    name: string;
    slug: string;
}

type Props = PageProps<{ rows: Paginated<Tag>; filters: Record<string, string | undefined> }>;

export default function BlogTags({ rows, filters }: Props) {
    return (
        <CrudPage<Tag>
            title="برچسب‌های مقالات"
            icon={<TagsOutlined />}
            routeKey="blog-tags"
            rows={rows}
            filters={filters}
            width={380}
            columns={[
                { title: 'نام', dataIndex: 'name', sorter: true },
                { title: 'اسلاگ', dataIndex: 'slug' },
            ]}
            fields={[
                { key: 'name', type: 'text', label: 'نام برچسب', required: true },
                { key: 'slug', type: 'slug', label: 'اسلاگ (اختیاری)' },
            ]}
            toForm={(r) => ({ name: r?.name ?? '', slug: r?.slug ?? '' })}
        />
    );
}
