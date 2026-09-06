import { Head, useForm } from '@inertiajs/react';
import { Card, Form, Input, Tabs, Divider, Select, DatePicker, Space } from 'antd';
import { ProjectOutlined } from '@ant-design/icons';
import dayjs from 'dayjs';
import EntityFormShell from '@/Components/EntityFormShell';
import RichTextEditor from '@/Components/RichTextEditor';
import MediaField from '@/Components/MediaField';
import StatusControl from '@/Components/StatusControl';
import SeoPanel from '@/Components/SeoPanel';
import type { PageProps } from '@/types/global';
import type { ContentStatus, SeoMetaForm, MediaItem } from '@/types/models';

interface Project {
    id: number;
    title: string;
    slug: string;
    client_name: string | null;
    customer_id: number | null;
    summary: string | null;
    body: string | null;
    cover_media_id: number | null;
    cover?: { thumb_url: string } | null;
    project_url: string | null;
    completed_on: string | null;
    featured: boolean;
    status: ContentStatus;
    published_at: string | null;
    sort_order: number;
    seo?: SeoMetaForm | null;
}

type Props = PageProps<{
    project: Project | null;
    gallery?: MediaItem[];
    customers: { id: number; name: string }[];
}>;

export default function ProjectForm({ project, gallery = [], customers }: Props) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>({
        title: project?.title ?? '',
        slug: project?.slug ?? '',
        client_name: project?.client_name ?? '',
        customer_id: project?.customer_id ?? null,
        summary: project?.summary ?? '',
        body: project?.body ?? '',
        cover_media_id: project?.cover_media_id ?? null,
        project_url: project?.project_url ?? '',
        completed_on: project?.completed_on ?? null,
        featured: project?.featured ?? false,
        status: (project?.status ?? 'draft') as ContentStatus,
        published_at: project?.published_at ?? null,
        sort_order: project?.sort_order ?? 0,
        gallery_ids: gallery.map((m) => m.id),
        seo: (project?.seo ?? {}) as Partial<SeoMetaForm>,
    });

    const save = () => {
        const opts = { preserveScroll: true };
        if (project) form.put(route('admin.projects.update', project.id), opts);
        else form.post(route('admin.projects.store'), opts);
    };

    const galleryIds: number[] = form.data.gallery_ids;

    return (
        <EntityFormShell
            title={project ? `ویرایش نمونه‌کار: ${project.title}` : 'نمونه‌کار جدید'}
            icon={<ProjectOutlined />}
            backRoute={route('admin.projects.index')}
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
                                        <Form.Item label="عنوان پروژه" required validateStatus={form.errors.title ? 'error' : ''} help={form.errors.title}>
                                            <Input value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="خلاصه">
                                            <Input.TextArea rows={3} value={form.data.summary} onChange={(e) => form.setData('summary', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="شرح پروژه">
                                            <RichTextEditor value={form.data.body} onChange={(v) => form.setData('body', v)} />
                                        </Form.Item>
                                        <Divider>گالری تصاویر</Divider>
                                        <Space wrap>
                                            {galleryIds.map((id, i) => (
                                                <MediaField
                                                    key={id}
                                                    value={id}
                                                    onChange={(newId) =>
                                                        form.setData(
                                                            'gallery_ids',
                                                            galleryIds.map((x, xi) => (xi === i ? newId : x)).filter(Boolean),
                                                        )
                                                    }
                                                />
                                            ))}
                                            <MediaField
                                                value={null}
                                                onChange={(id) => id && form.setData('gallery_ids', [...galleryIds, id])}
                                            />
                                        </Space>
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
                    <Form.Item label="نام کارفرما">
                        <Input value={form.data.client_name} onChange={(e) => form.setData('client_name', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="مشتری مرتبط">
                        <Select
                            allowClear
                            value={form.data.customer_id ?? undefined}
                            options={customers.map((c) => ({ label: c.name, value: c.id }))}
                            onChange={(v) => form.setData('customer_id', v ?? null)}
                        />
                    </Form.Item>
                    <Form.Item label="تصویر شاخص">
                        <MediaField
                            value={form.data.cover_media_id}
                            previewUrl={project?.cover?.thumb_url ?? null}
                            onChange={(id) => form.setData('cover_media_id', id)}
                        />
                    </Form.Item>
                    <Form.Item label="تاریخ اتمام">
                        <DatePicker
                            style={{ width: '100%' }}
                            value={form.data.completed_on ? dayjs(form.data.completed_on) : null}
                            onChange={(d) => form.setData('completed_on', d ? d.format('YYYY-MM-DD') : null)}
                        />
                    </Form.Item>
                    <Form.Item label="لینک پروژه">
                        <Input dir="ltr" value={form.data.project_url} onChange={(e) => form.setData('project_url', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="ترتیب">
                        <Input type="number" value={form.data.sort_order} onChange={(e) => form.setData('sort_order', Number(e.target.value))} />
                    </Form.Item>
                </Form>
            }
        >
            <Head title={project ? project.title : 'نمونه‌کار جدید'} />
        </EntityFormShell>
    );
}
