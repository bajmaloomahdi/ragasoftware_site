import { Head, useForm } from '@inertiajs/react';
import { Card, Form, Input, Select, Tabs, Typography, Divider } from 'antd';
import { ShopOutlined } from '@ant-design/icons';
import EntityFormShell from '@/Components/EntityFormShell';
import RichTextEditor from '@/Components/RichTextEditor';
import MediaField from '@/Components/MediaField';
import StatusControl from '@/Components/StatusControl';
import SeoPanel from '@/Components/SeoPanel';
import FeatureRepeater, { type FeatureRow } from '@/Components/FeatureRepeater';
import type { PageProps } from '@/types/global';
import type { ContentStatus, SeoMetaForm } from '@/types/models';

interface Product {
    id: number;
    title: string;
    slug: string;
    category_id: number | null;
    tagline: string | null;
    summary: string | null;
    body: string | null;
    icon: string | null;
    hero_media_id: number | null;
    hero_image?: { thumb_url: string } | null;
    featured: boolean;
    cta_label: string | null;
    cta_url: string | null;
    status: ContentStatus;
    published_at: string | null;
    sort_order: number;
    features?: FeatureRow[];
    seo?: SeoMetaForm | null;
}

type Props = PageProps<{
    product: Product | null;
    categories: { id: number; name: string }[];
}>;

export default function ProductForm({ product, categories }: Props) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>({
        title: product?.title ?? '',
        slug: product?.slug ?? '',
        category_id: product?.category_id ?? null,
        tagline: product?.tagline ?? '',
        summary: product?.summary ?? '',
        body: product?.body ?? '',
        icon: product?.icon ?? '',
        hero_media_id: product?.hero_media_id ?? null,
        featured: product?.featured ?? false,
        cta_label: product?.cta_label ?? '',
        cta_url: product?.cta_url ?? '',
        status: (product?.status ?? 'draft') as ContentStatus,
        published_at: product?.published_at ?? null,
        sort_order: product?.sort_order ?? 0,
        features: (product?.features ?? []) as FeatureRow[],
        seo: (product?.seo ?? {}) as Partial<SeoMetaForm>,
    });

    const save = () => {
        const opts = { preserveScroll: true };
        if (product) form.put(route('admin.products.update', product.id), opts);
        else form.post(route('admin.products.store'), opts);
    };

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const set = (k: string, v: any) => form.setData(k, v);

    return (
        <EntityFormShell
            title={product ? `ویرایش محصول: ${product.title}` : 'محصول جدید'}
            icon={<ShopOutlined />}
            backRoute={route('admin.products.index')}
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
                                        <Form.Item label="عنوان محصول" required validateStatus={form.errors.title ? 'error' : ''} help={form.errors.title}>
                                            <Input value={form.data.title} onChange={(e) => set('title', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="شعار / زیرعنوان">
                                            <Input value={form.data.tagline} onChange={(e) => set('tagline', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="خلاصه (برای کارت‌ها و لیست‌ها)">
                                            <Input.TextArea rows={3} value={form.data.summary} onChange={(e) => set('summary', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="متن کامل">
                                            <RichTextEditor value={form.data.body} onChange={(v) => set('body', v)} />
                                        </Form.Item>
                                        <Divider>دکمهٔ فراخوان (CTA)</Divider>
                                        <Form.Item label="متن دکمه">
                                            <Input value={form.data.cta_label} onChange={(e) => set('cta_label', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="لینک دکمه">
                                            <Input dir="ltr" value={form.data.cta_url} onChange={(e) => set('cta_url', e.target.value)} />
                                        </Form.Item>
                                    </Form>
                                </Card>
                            ),
                        },
                        {
                            key: 'features',
                            label: 'قابلیت‌ها',
                            children: (
                                <Card>
                                    <Typography.Paragraph type="secondary">
                                        قابلیت‌های کلیدی محصول که در صفحهٔ محصول نمایش داده می‌شوند.
                                    </Typography.Paragraph>
                                    <FeatureRepeater value={form.data.features} onChange={(v) => set('features', v)} />
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
                                        onChange={(patch) => set('seo', { ...form.data.seo, ...patch })}
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
                        <Input dir="ltr" placeholder="erp" value={form.data.slug} onChange={(e) => set('slug', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="دسته‌بندی">
                        <Select
                            allowClear
                            value={form.data.category_id ?? undefined}
                            options={categories.map((c) => ({ label: c.name, value: c.id }))}
                            onChange={(v) => set('category_id', v ?? null)}
                        />
                    </Form.Item>
                    <Form.Item label="تصویر شاخص / ماک‌آپ">
                        <MediaField
                            value={form.data.hero_media_id}
                            previewUrl={product?.hero_image?.thumb_url ?? null}
                            onChange={(id) => set('hero_media_id', id)}
                        />
                    </Form.Item>
                    <Form.Item label="نام آیکن (اختیاری)">
                        <Input value={form.data.icon} onChange={(e) => set('icon', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="ترتیب نمایش">
                        <Input type="number" value={form.data.sort_order} onChange={(e) => set('sort_order', Number(e.target.value))} />
                    </Form.Item>
                    <Form.Item label="نمایش در بخش محصولات ویژه">
                        <Select
                            value={form.data.featured ? 1 : 0}
                            options={[{ label: 'خیر', value: 0 }, { label: 'بله', value: 1 }]}
                            onChange={(v) => set('featured', Boolean(v))}
                        />
                    </Form.Item>
                </Form>
            }
        >
            <Head title={product ? product.title : 'محصول جدید'} />
        </EntityFormShell>
    );
}
