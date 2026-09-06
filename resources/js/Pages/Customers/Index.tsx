import { Tag, Image } from 'antd';
import { TeamOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Customer {
    id: number;
    name: string;
    slug: string;
    industry: string | null;
    website_url: string | null;
    logo_media_id: number | null;
    logo: { thumb_url: string } | null;
    is_featured: boolean;
    is_active: boolean;
    sort_order: number;
}

type Props = PageProps<{ rows: Paginated<Customer>; filters: Record<string, string | undefined> }>;

export default function CustomersIndex({ rows, filters }: Props) {
    return (
        <CrudPage<Customer>
            title="مشتریان"
            subtitle="لوگو و اطلاعات مشتریان سازمانی"
            icon={<TeamOutlined />}
            routeKey="customers"
            rows={rows}
            filters={filters}
            searchPlaceholder="جستجوی نام یا صنعت"
            columns={[
                {
                    title: 'لوگو',
                    dataIndex: ['logo', 'thumb_url'],
                    width: 72,
                    render: (v: string) => (v ? <Image src={v} width={44} height={44} style={{ objectFit: 'contain' }} /> : '—'),
                },
                { title: 'نام', dataIndex: 'name', sorter: true },
                { title: 'صنعت', dataIndex: 'industry', render: (v) => v || '—' },
                {
                    title: 'وضعیت',
                    dataIndex: 'is_active',
                    render: (v: boolean, r) => (
                        <>
                            <Tag color={v ? 'green' : 'default'}>{v ? 'فعال' : 'غیرفعال'}</Tag>
                            {r.is_featured && <Tag color="blue">ویژه</Tag>}
                        </>
                    ),
                },
            ]}
            fields={[
                { key: 'name', type: 'text', label: 'نام مشتری', required: true },
                { key: 'slug', type: 'slug', label: 'اسلاگ (اختیاری)' },
                { key: 'industry', type: 'text', label: 'صنعت / حوزه' },
                { key: 'website_url', type: 'url', label: 'وب‌سایت' },
                { key: 'logo_media_id', type: 'media', label: 'لوگو' },
                { key: 'is_featured', type: 'switch', label: 'نمایش در بخش‌های ویژه' },
                { key: 'is_active', type: 'switch', label: 'فعال' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({
                name: r?.name ?? '',
                slug: r?.slug ?? '',
                industry: r?.industry ?? '',
                website_url: r?.website_url ?? '',
                logo_media_id: r?.logo_media_id ?? null,
                is_featured: r?.is_featured ?? false,
                is_active: r?.is_active ?? true,
                sort_order: r?.sort_order ?? 0,
            })}
        />
    );
}
