import { Head, useForm } from '@inertiajs/react';
import { Card, Form, Input, Tabs, Divider, Select } from 'antd';
import { ReadOutlined } from '@ant-design/icons';
import EntityFormShell from '@/Components/EntityFormShell';
import RichTextEditor from '@/Components/RichTextEditor';
import MediaField from '@/Components/MediaField';
import StatusControl from '@/Components/StatusControl';
import SeoPanel from '@/Components/SeoPanel';
import type { PageProps } from '@/types/global';
import type { ContentStatus, SeoMetaForm } from '@/types/models';

interface Post {
    id: number;
    title: string;
    slug: string;
    category_id: number | null;
    author_id: number | null;
    excerpt: string | null;
    body: string | null;
    cover_media_id: number | null;
    cover?: { thumb_url: string } | null;
    is_featured: boolean;
    status: ContentStatus;
    published_at: string | null;
    tags?: { id: number }[];
    seo?: SeoMetaForm | null;
}

type Props = PageProps<{
    post: Post | null;
    categories: { id: number; name: string }[];
    tags: { id: number; name: string }[];
    authors: { id: number; name: string }[];
}>;

export default function BlogForm({ post, categories, tags, authors }: Props) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const form = useForm<any>({
        title: post?.title ?? '',
        slug: post?.slug ?? '',
        category_id: post?.category_id ?? null,
        author_id: post?.author_id ?? null,
        excerpt: post?.excerpt ?? '',
        body: post?.body ?? '',
        cover_media_id: post?.cover_media_id ?? null,
        is_featured: post?.is_featured ?? false,
        status: (post?.status ?? 'draft') as ContentStatus,
        published_at: post?.published_at ?? null,
        tag_ids: (post?.tags ?? []).map((t) => t.id),
        seo: (post?.seo ?? {}) as Partial<SeoMetaForm>,
    });

    const save = () => {
        const opts = { preserveScroll: true };
        if (post) form.put(route('admin.posts.update', post.id), opts);
        else form.post(route('admin.posts.store'), opts);
    };

    return (
        <EntityFormShell
            title={post ? `ویرایش مقاله: ${post.title}` : 'مقاله جدید'}
            icon={<ReadOutlined />}
            backRoute={route('admin.posts.index')}
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
                                        <Form.Item label="خلاصه (چکیده)">
                                            <Input.TextArea rows={3} value={form.data.excerpt} onChange={(e) => form.setData('excerpt', e.target.value)} />
                                        </Form.Item>
                                        <Form.Item label="متن مقاله">
                                            <RichTextEditor value={form.data.body} onChange={(v) => form.setData('body', v)} minHeight={360} />
                                        </Form.Item>
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
                    <Divider />
                    <Form.Item label="اسلاگ (URL)" help={form.errors.slug}>
                        <Input dir="ltr" value={form.data.slug} onChange={(e) => form.setData('slug', e.target.value)} />
                    </Form.Item>
                    <Form.Item label="دسته">
                        <Select
                            allowClear
                            value={form.data.category_id ?? undefined}
                            options={categories.map((c) => ({ label: c.name, value: c.id }))}
                            onChange={(v) => form.setData('category_id', v ?? null)}
                        />
                    </Form.Item>
                    <Form.Item label="برچسب‌ها">
                        <Select
                            mode="multiple"
                            value={form.data.tag_ids}
                            options={tags.map((t) => ({ label: t.name, value: t.id }))}
                            onChange={(v) => form.setData('tag_ids', v)}
                        />
                    </Form.Item>
                    <Form.Item label="نویسنده">
                        <Select
                            allowClear
                            value={form.data.author_id ?? undefined}
                            options={authors.map((a) => ({ label: a.name, value: a.id }))}
                            onChange={(v) => form.setData('author_id', v ?? null)}
                        />
                    </Form.Item>
                    <Form.Item label="تصویر شاخص">
                        <MediaField
                            value={form.data.cover_media_id}
                            previewUrl={post?.cover?.thumb_url ?? null}
                            onChange={(id) => form.setData('cover_media_id', id)}
                        />
                    </Form.Item>
                    <Form.Item label="مقالهٔ ویژه">
                        <Select
                            value={form.data.is_featured ? 1 : 0}
                            options={[{ label: 'خیر', value: 0 }, { label: 'بله', value: 1 }]}
                            onChange={(v) => form.setData('is_featured', Boolean(v))}
                        />
                    </Form.Item>
                </Form>
            }
        >
            <Head title={post ? post.title : 'مقاله جدید'} />
        </EntityFormShell>
    );
}
