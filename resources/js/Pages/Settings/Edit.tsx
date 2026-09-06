import { Head, useForm } from '@inertiajs/react';
import { Button, Card, Form, Input, InputNumber, Switch, Tabs } from 'antd';
import { SettingOutlined } from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import MediaField from '@/Components/MediaField';
import type { PageProps } from '@/types/global';

interface FieldDef {
    key: string;
    group: string;
    type: 'string' | 'text' | 'html' | 'boolean' | 'number' | 'media' | 'json';
    label: string;
    hint?: string;
}

type Props = PageProps<{
    groups: Record<string, string>;
    schema: FieldDef[];
    values: Record<string, unknown>;
}>;

export default function SettingsEdit({ groups, schema, values }: Props) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>({ values });

    const set = (key: string, value: unknown) =>
        form.setData('values', { ...form.data.values, [key]: value });

    const renderField = (f: FieldDef) => {
        const v = form.data.values[f.key];
        switch (f.type) {
            case 'boolean':
                return (
                    <Form.Item key={f.key} label={f.label} extra={f.hint} valuePropName="checked">
                        <Switch checked={Boolean(v)} onChange={(x) => set(f.key, x)} />
                    </Form.Item>
                );
            case 'number':
                return (
                    <Form.Item key={f.key} label={f.label} extra={f.hint}>
                        <InputNumber style={{ width: '100%' }} value={v as number} onChange={(x) => set(f.key, x)} />
                    </Form.Item>
                );
            case 'text':
                return (
                    <Form.Item key={f.key} label={f.label} extra={f.hint}>
                        <Input.TextArea rows={3} value={(v as string) ?? ''} onChange={(e) => set(f.key, e.target.value)} />
                    </Form.Item>
                );
            case 'media':
                return (
                    <Form.Item key={f.key} label={f.label} extra={f.hint}>
                        <MediaField value={(v as number) ?? null} onChange={(id) => set(f.key, id)} />
                    </Form.Item>
                );
            default:
                return (
                    <Form.Item key={f.key} label={f.label} extra={f.hint}>
                        <Input value={(v as string) ?? ''} onChange={(e) => set(f.key, e.target.value)} />
                    </Form.Item>
                );
        }
    };

    const tabs = Object.entries(groups).map(([key, label]) => ({
        key,
        label,
        children: (
            <Card>
                <Form layout="vertical">{schema.filter((f) => f.group === key).map(renderField)}</Form>
            </Card>
        ),
    }));

    return (
        <AdminLayout>
            <Head title="تنظیمات سایت" />
            <PageHeader
                title="تنظیمات سایت"
                subtitle="اطلاعات پایه، تماس، فوتر، اسکریپت‌ها و پیش‌فرض‌های سئو"
                icon={<SettingOutlined />}
                actions={
                    <Button
                        type="primary"
                        loading={form.processing}
                        onClick={() => form.put(route('admin.settings.update'), { preserveScroll: true })}
                    >
                        ذخیره تغییرات
                    </Button>
                }
            />
            <Tabs items={tabs} />
        </AdminLayout>
    );
}
