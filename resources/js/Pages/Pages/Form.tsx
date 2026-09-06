import { Head, useForm } from '@inertiajs/react';
import { Card, Form, Input, Select, Tabs, Switch } from 'antd';
import { FileTextOutlined } from '@ant-design/icons';
import EntityFormShell from '@/Components/EntityFormShell';
import StatusControl from '@/Components/StatusControl';
import SeoPanel from '@/Components/SeoPanel';
import SectionBuilder, { type SectionRow, type SectionType } from '@/Components/SectionBuilder';
import type { PageProps } from '@/types/global';
import type { ContentStatus, SeoMetaForm } from '@/types/models';

interface PageModel {
    id: number;
    title: string;
    slug: string;
    template: string;
    excerpt: string | null;
    status: ContentStatus;
    published_at: string | null;
    is_homepage: boolean;
    show_in_sitemap: boolean;
    sort_order: number;
    sections?: Array<{ id: number; type: string; name: string | null; settings: Record<string, unknown>; is_active: boolean }>;
    seo?: SeoMetaForm | null;
}

type Props = PageProps<{ page: PageModel | null; sectionTypes: SectionType[] }>;

let k = 0;

export default function PageForm({ page, sectionTypes }: Props) {
    const initialSections: SectionRow[] = (page?.sections ?? []).map((s) => ({
        key: `db-${s.id}`,
        id: s.id,
        type: s.type,
        name: s.name ?? '',
        settings: s.settings ?? {},
        is_active: s.is_active,
    }));

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>({
        title: page?.title ?? '',
        slug: page?.slug ?? '',
        template: page?.template ?? 'default',
        excerpt: page?.excerpt ?? '',
        status: (page?.status ?? 'draft') as ContentStatus,
        published_at: page?.published_at ?? null,
        show_in_sitemap: page?.show_in_sitemap ?? true,
        sort_order: page?.sort_order ?? 0,
        sections: initialSections,
        seo: (page?.seo ?? {}) as Partial<SeoMetaForm>,
    });

    const save = () => {
        const payload = {
            ...form.data,
            sections: (form.data.sections as SectionRow[]).map((s, i) => ({
                id: typeof s.id === 'number' ? s.id : null,
                type: s.type,
                name: s.name,
                settings: s.settings,
                is_active: s.is_active,
                sort_order: i,
            })),
        };
        form.transform(() => payload);
        if (page) form.put(route('admin.pages.update', page.id), { preserveScroll: true });
        else form.post(route('admin.pages.store'), { preserveScroll: true });
    };

    return (
        <EntityFormShell
            title={page ? (page.is_homepage ? 'صفحه اصلی' : `ویرایش صفحه: ${page.title}`) : 'صفحه جدید'}
            icon={<FileTextOutlined />}
            backRoute={route('admin.pages.index')}
            processing={form.processing}
            onSave={save}
            main={
                <Tabs
                    items={[
                        {
                            key: 'sections',
                            label: 'بخش‌های صفحه',
                            children: (
                                <Card>
                                    <SectionBuilder
                                        value={form.data.sections}
                                        types={sectionTypes}
                                        onChange={(rows) => form.setData('sections', rows)}
                                    />
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
                                        fallbackDescription={form.data.excerpt}
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
                    <Form.Item label="عنوان صفحه" required validateStatus={form.errors.title ? 'error' : ''} help={form.errors.title}>
                        <Input value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} disabled={page?.is_homepage} />
                    </Form.Item>
                    {!page?.is_homepage && (
                        <Form.Item label="اسلاگ (URL)" help={form.errors.slug}>
                            <Input dir="ltr" value={form.data.slug} onChange={(e) => form.setData('slug', e.target.value)} />
                        </Form.Item>
                    )}
                    <Form.Item label="قالب">
                        <Select
                            value={form.data.template}
                            options={[
                                { label: 'پیش‌فرض', value: 'default' },
                                { label: 'تمام‌عرض', value: 'full_width' },
                                { label: 'لندینگ', value: 'landing' },
                            ]}
                            onChange={(v) => form.setData('template', v)}
                        />
                    </Form.Item>
                    <Form.Item label="خلاصه">
                        <Input.TextArea rows={3} value={form.data.excerpt} onChange={(e) => form.setData('excerpt', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="نمایش در نقشه سایت" valuePropName="checked">
                        <Switch checked={form.data.show_in_sitemap} onChange={(v) => form.setData('show_in_sitemap', v)} />
                    </Form.Item>
                </Form>
            }
        >
            <Head title={page ? page.title : 'صفحه جدید'} />
        </EntityFormShell>
    );
}
