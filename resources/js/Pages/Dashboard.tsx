import { Head } from '@inertiajs/react';
import { Typography } from 'antd';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Dashboard() {
    return (
        <AdminLayout>
            <Head title="داشبورد" />
            <Typography.Title level={3}>داشبورد مدیریت وب‌سایت</Typography.Title>
            <Typography.Paragraph type="secondary">
                خوش آمدید. از منوی کناری بخش مورد نظر را برای مدیریت محتوای وب‌سایت انتخاب کنید.
            </Typography.Paragraph>
        </AdminLayout>
    );
}
