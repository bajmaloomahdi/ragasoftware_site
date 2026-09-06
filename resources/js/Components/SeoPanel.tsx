import { Form, Input, Switch, Select, Collapse, Typography } from 'antd';
import MediaField from './MediaField';
import type { SeoMetaForm } from '@/types/models';

interface Props {
    value: Partial<SeoMetaForm>;
    onChange: (patch: Partial<SeoMetaForm>) => void;
    /** live fallbacks so the editor sees what Google will get if a field is blank */
    fallbackTitle?: string;
    fallbackDescription?: string;
}

const ROBOTS = [
    { label: 'index, follow (پیش‌فرض)', value: 'index, follow' },
    { label: 'noindex, follow', value: 'noindex, follow' },
    { label: 'index, nofollow', value: 'index, nofollow' },
    { label: 'noindex, nofollow', value: 'noindex, nofollow' },
];

const SCHEMA_TYPES = [
    { label: 'خودکار', value: '' },
    { label: 'WebPage', value: 'WebPage' },
    { label: 'Article', value: 'Article' },
    { label: 'Product', value: 'Product' },
    { label: 'Service', value: 'Service' },
];

export default function SeoPanel({ value, onChange, fallbackTitle, fallbackDescription }: Props) {
    const set = (k: keyof SeoMetaForm, v: unknown) => onChange({ [k]: v });

    return (
        <Collapse
            defaultActiveKey={['basic']}
            items={[
                {
                    key: 'basic',
                    label: 'سئو پایه',
                    children: (
                        <>
                            <Form.Item
                                label="عنوان سئو (Meta Title)"
                                help={
                                    !value.meta_title && fallbackTitle
                                        ? `در صورت خالی بودن: «${fallbackTitle}»`
                                        : `${(value.meta_title ?? '').length}/60`
                                }
                            >
                                <Input
                                    maxLength={70}
                                    value={value.meta_title ?? ''}
                                    onChange={(e) => set('meta_title', e.target.value)}
                                />
                            </Form.Item>
                            <Form.Item
                                label="توضیح متا (Meta Description)"
                                help={
                                    !value.meta_description && fallbackDescription
                                        ? 'در صورت خالی بودن از خلاصه محتوا استفاده می‌شود'
                                        : `${(value.meta_description ?? '').length}/160`
                                }
                            >
                                <Input.TextArea
                                    rows={3}
                                    maxLength={200}
                                    value={value.meta_description ?? ''}
                                    onChange={(e) => set('meta_description', e.target.value)}
                                />
                            </Form.Item>
                            <Form.Item label="کلیدواژه کانونی (Focus Keyword)">
                                <Input
                                    value={value.focus_keyword ?? ''}
                                    onChange={(e) => set('focus_keyword', e.target.value)}
                                />
                            </Form.Item>
                        </>
                    ),
                },
                {
                    key: 'advanced',
                    label: 'پیشرفته',
                    children: (
                        <>
                            <Form.Item label="Canonical URL">
                                <Input
                                    dir="ltr"
                                    placeholder="https://…"
                                    value={value.canonical_url ?? ''}
                                    onChange={(e) => set('canonical_url', e.target.value)}
                                />
                            </Form.Item>
                            <Form.Item label="Meta Robots">
                                <Select
                                    options={ROBOTS}
                                    value={value.meta_robots ?? 'index, follow'}
                                    onChange={(v) => set('meta_robots', v)}
                                />
                            </Form.Item>
                            <Form.Item label="نوع Structured Data">
                                <Select
                                    options={SCHEMA_TYPES}
                                    value={value.schema_type ?? ''}
                                    onChange={(v) => set('schema_type', v || null)}
                                />
                            </Form.Item>
                            <Form.Item label="خارج‌کردن از ایندکس گوگل">
                                <Switch
                                    checked={Boolean(value.no_index)}
                                    onChange={(v) => set('no_index', v)}
                                />
                            </Form.Item>
                        </>
                    ),
                },
                {
                    key: 'social',
                    label: 'شبکه‌های اجتماعی (Open Graph / Twitter)',
                    children: (
                        <>
                            <Typography.Paragraph type="secondary" style={{ marginTop: 0 }}>
                                در صورت خالی بودن، از عنوان و توضیح سئو استفاده می‌شود.
                            </Typography.Paragraph>
                            <Form.Item label="OG Title">
                                <Input
                                    value={value.og_title ?? ''}
                                    onChange={(e) => set('og_title', e.target.value)}
                                />
                            </Form.Item>
                            <Form.Item label="OG Description">
                                <Input.TextArea
                                    rows={2}
                                    value={value.og_description ?? ''}
                                    onChange={(e) => set('og_description', e.target.value)}
                                />
                            </Form.Item>
                            <Form.Item label="تصویر اشتراک‌گذاری (OG Image)">
                                <MediaField
                                    value={value.og_media_id ?? null}
                                    onChange={(id) => set('og_media_id', id)}
                                />
                            </Form.Item>
                        </>
                    ),
                },
            ]}
        />
    );
}
