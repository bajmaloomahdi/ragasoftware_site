import { Tag } from 'antd';
import { UserOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface User {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    roles: { name: string }[];
}

type Props = PageProps<{
    rows: Paginated<User>;
    filters: Record<string, string | undefined>;
    roles: string[];
}>;

export default function UsersIndex({ rows, filters, roles }: Props) {
    return (
        <CrudPage<User>
            title="کاربران"
            subtitle="مدیریت کاربران پنل و نقش‌های آن‌ها"
            icon={<UserOutlined />}
            routeKey="users"
            rows={rows}
            filters={filters}
            width={480}
            searchPlaceholder="جستجوی نام یا ایمیل"
            columns={[
                { title: 'نام', dataIndex: 'name' },
                { title: 'ایمیل', dataIndex: 'email', render: (v) => <span dir="ltr">{v}</span> },
                {
                    title: 'نقش‌ها',
                    dataIndex: 'roles',
                    render: (rs: { name: string }[]) => rs.map((r) => <Tag key={r.name}>{r.name}</Tag>),
                },
                {
                    title: 'وضعیت',
                    dataIndex: 'is_active',
                    width: 90,
                    render: (v: boolean) => <Tag color={v ? 'green' : 'default'}>{v ? 'فعال' : 'غیرفعال'}</Tag>,
                },
            ]}
            fields={[
                { key: 'name', type: 'text', label: 'نام', required: true },
                { key: 'email', type: 'email', label: 'ایمیل', required: true },
                { key: 'password', type: 'text', label: 'گذرواژه (برای تغییر پر کنید)' },
                { key: 'password_confirmation', type: 'text', label: 'تکرار گذرواژه' },
                {
                    key: 'roles',
                    type: 'multiselect',
                    label: 'نقش‌ها',
                    options: roles.map((r) => ({ label: r, value: r })),
                },
                { key: 'is_active', type: 'switch', label: 'فعال' },
            ]}
            toForm={(r) => ({
                name: r?.name ?? '',
                email: r?.email ?? '',
                password: '',
                password_confirmation: '',
                roles: r?.roles?.map((x) => x.name) ?? [],
                is_active: r?.is_active ?? true,
            })}
        />
    );
}
