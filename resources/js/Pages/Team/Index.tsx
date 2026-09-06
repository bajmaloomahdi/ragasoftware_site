import { Tag, Avatar } from 'antd';
import { IdcardOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Member {
    id: number;
    name: string;
    slug: string;
    role_title: string | null;
    bio: string | null;
    email: string | null;
    linkedin_url: string | null;
    photo_media_id: number | null;
    photo: { thumb_url: string } | null;
    is_active: boolean;
    sort_order: number;
}

type Props = PageProps<{ rows: Paginated<Member>; filters: Record<string, string | undefined> }>;

export default function TeamIndex({ rows, filters }: Props) {
    return (
        <CrudPage<Member>
            title="اعضای تیم"
            icon={<IdcardOutlined />}
            routeKey="team"
            rows={rows}
            filters={filters}
            columns={[
                {
                    title: '',
                    dataIndex: ['photo', 'thumb_url'],
                    width: 56,
                    render: (v: string) => <Avatar src={v} size={40}>؟</Avatar>,
                },
                { title: 'نام', dataIndex: 'name', sorter: true },
                { title: 'سمت', dataIndex: 'role_title', render: (v) => v || '—' },
                { title: 'وضعیت', dataIndex: 'is_active', render: (v: boolean) => <Tag color={v ? 'green' : 'default'}>{v ? 'فعال' : 'غیرفعال'}</Tag> },
            ]}
            fields={[
                { key: 'name', type: 'text', label: 'نام و نام‌خانوادگی', required: true },
                { key: 'role_title', type: 'text', label: 'سمت' },
                { key: 'bio', type: 'textarea', label: 'معرفی کوتاه', rows: 4 },
                { key: 'photo_media_id', type: 'media', label: 'عکس' },
                { key: 'email', type: 'email', label: 'ایمیل' },
                { key: 'linkedin_url', type: 'url', label: 'لینکدین' },
                { key: 'is_active', type: 'switch', label: 'فعال' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({
                name: r?.name ?? '',
                role_title: r?.role_title ?? '',
                bio: r?.bio ?? '',
                photo_media_id: r?.photo_media_id ?? null,
                email: r?.email ?? '',
                linkedin_url: r?.linkedin_url ?? '',
                is_active: r?.is_active ?? true,
                sort_order: r?.sort_order ?? 0,
            })}
        />
    );
}
