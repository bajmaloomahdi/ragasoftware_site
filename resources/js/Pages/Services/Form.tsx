import { Head, useForm } from '@inertiajs/react';
import { Card, Form, Input, Tabs, Divider, Select } from 'antd';
import { ToolOutlined } from '@ant-design/icons';
import EntityFormShell from '@/Components/EntityFormShell';
import RichTextEditor from '@/Components/RichTextEditor';
import MediaField from '@/Components/MediaField';
import StatusControl from '@/Components/StatusControl';
import SeoPanel from '@/Components/SeoPanel';
import FeatureRepeater, { type FeatureRow } from '@/Components/FeatureRepeater';
import type { PageProps } from '@/types/global';
import type { ContentStatus, SeoMetaForm } from '@/types/models';

interface Service {
    id: number;
    title: string;
    slug: string;
    summary: string | null;
    body: string | null;
    icon: string | null;
    media_id: number | null;
    image?: { thumb_url: string } | null;
    featured: boolean;
    status: ContentStatus;
    published_at: string | null;
    sort_order: number;
    features?: FeatureRow[];
    seo?: SeoMetaForm | null;
}

type Props = PageProps<{ service: Service | null }>;

export default function ServiceForm({ service }: Props) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>({
        title: service?.title ?? '',
        slug: service?.slug ?? '',
        summary: service?.summary ?? '',
        body: service?.body ?? '',
        icon: service?.icon ?? '',
        media_id: service?.media_id ?? null,
        featured: service?.featured ?? false,
        status: (service?.status ?? 'draft') as ContentStatus,
        published_at: service?.published_at ?? null,
        sort_order: service?.sort_order ?? 0,
        features: (service?.features ?? []) as FeatureRow[],
        seo: (service?.seo ?? {}) as Partial<SeoMetaForm>,
    });

    const save = () => {
        const opts = { preserveScroll: true };
        if (service) form.put(route('admin.services.update', service.id), opts);
        else form.post(route('admin.services.store'), opts);
    };

    return (
        <EntityFormShell
            title={service ? `ویرایش خدمت: ${service.title}` : 'خدمت جدید'}
            icon={<ToolOutlined />}
            backRoute={route('admin.services.index')}
            processing={form.processing}
            onSave={save}
            main={
                <Tabs
                    items={[
                        {
                            key: 'content',
                            label: 'محتوا',
                            children: (
                                <Card>
                                    <Form layout="vertical">
                                        <Form.Item label="عنوان" required validateStatus={form.errors.title ? 'error' : ''} help={form.errors.title}>
                                            <Input value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="خلاصه">
                                            <Input.TextArea rows={3} value={form.data.summary} onChange={(e) => form.setData('summary', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="متن کامل">
                                            <RichTextEditor value={form.data.body} onChange={(v) => form.setData('body', v)} />
                                        </Form.Item>
                                        <Divider>ویژگی‌ها</Divider>
                                        <FeatureRepeater value={form.data.features} onChange={(v) => form.setData('features', v)} />
                                    </Form>
                                </Card>
                            ),
                        },
                        {
                            key: 'seo',
                            label: 'سئو',
                            children: (
                                <Card>
                                    <SeoPanel
                                        value={form.data.seo}
                                        onChange={(patch) => form.setData('seo', { ...form.data.seo, ...patch })}
                                        fallbackTitle={form.data.title}
                                        fallbackDescription={form.data.summary}
                                    />
                                </Card>
                            ),
                        },
                    ]}
                />
            }
            side={
                <Form layout="vertical">
                    <StatusControl
                        status={form.data.status}
                        publishedAt={form.data.published_at}
                        onChange={(patch) => form.setData({ ...form.data, ...patch })}
                    />
                    <Divider />
                    <Form.Item label="اسلاگ (URL)" help={form.errors.slug}>
                        <Input dir="ltr" value={form.data.slug} onChange={(e) => form.setData('slug', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="تصویر">
                        <MediaField
                            value={form.data.media_id}
                            previewUrl={service?.image?.thumb_url ?? null}
                            onChange={(id) => form.setData('media_id', id)}
                        />
                    </Form.Item>
                    <Form.Item label="نام آیکن">
                        <Input value={form.data.icon} onChange={(e) => form.setData('icon', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="ترتیب">
                        <Input type="number" value={form.data.sort_order} onChange={(e) => form.setData('sort_order', Number(e.target.value))} />
                    </Form.Item>
                    <Form.Item label="خدمت ویژه">
                        <Select
                            value={form.data.featured ? 1 : 0}
                            options={[{ label: 'خیر', value: 0 }, { label: 'بله', value: 1 }]}
                            onChange={(v) => form.setData('featured', Boolean(v))}
                        />
                    </Form.Item>
                </Form>
            }
        >
            <Head title={service ? service.title : 'خدمت جدید'} />
        </EntityFormShell>
    );
}
