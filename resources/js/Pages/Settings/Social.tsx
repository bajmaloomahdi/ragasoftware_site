import { Tag } from 'antd';
import { ShareAltOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface SocialLink {
    id: number;
    platform: string;
    label: string | null;
    url: string;
    icon: string | null;
    is_active: boolean;
    sort_order: number;
}

type Props = PageProps<{ rows: Paginated<SocialLink>; filters: Record<string, string | undefined> }>;

const PLATFORMS = ['instagram', 'linkedin', 'telegram', 'x', 'youtube', 'aparat', 'github', 'whatsapp'];

export default function SocialLinks({ rows, filters }: Props) {
    return (
        <CrudPage<SocialLink>
            title="شبکه‌های اجتماعی"
            icon={<ShareAltOutlined />}
            routeKey="social-links"
            rows={rows}
            filters={filters}
            width={400}
            columns={[
                { title: 'پلتفرم', dataIndex: 'platform' },
                { title: 'نشانی', dataIndex: 'url', render: (v: string) => <span dir="ltr">{v}</span> },
                { title: 'وضعیت', dataIndex: 'is_active', render: (v: boolean) => <Tag color={v ? 'green' : 'default'}>{v ? 'فعال' : 'غیرفعال'}</Tag> },
            ]}
            fields={[
                {
                    key: 'platform',
                    type: 'select',
                    label: 'پلتفرم',
                    required: true,
                    options: PLATFORMS.map((p) => ({ label: p, value: p })),
                },
                { key: 'label', type: 'text', label: 'برچسب (اختیاری)' },
                { key: 'url', type: 'url', label: 'نشانی کامل', required: true },
                { key: 'icon', type: 'text', label: 'نام آیکن (اختیاری)' },
                { key: 'is_active', type: 'switch', label: 'فعال' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({
                platform: r?.platform ?? 'instagram',
                label: r?.label ?? '',
                url: r?.url ?? '',
                icon: r?.icon ?? '',
                is_active: r?.is_active ?? true,
                sort_order: r?.sort_order ?? 0,
            })}
        />
    );
}
