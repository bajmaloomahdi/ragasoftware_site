import { Button, Card, Form, Input, InputNumber, Select, Switch, Space } from 'antd';
import { PlusOutlined, DeleteOutlined } from '@ant-design/icons';
import RichTextEditor from './RichTextEditor';
import MediaField from './MediaField';

export interface SectionFieldSpec {
    key: string;
    type: string;
    label: string;
    required?: boolean;
    options?: Record<string, string>;
    default?: unknown;
    item?: SectionFieldSpec[];
    when?: Record<string, unknown>;
}

interface Props {
    fields: SectionFieldSpec[];
    values: Record<string, unknown>;
    onChange: (values: Record<string, unknown>) => void;
}

/** Renders the editable fields of one page section from its registry schema. */
export default function SectionFields({ fields, values, onChange }: Props) {
    const set = (key: string, value: unknown) => onChange({ ...values, [key]: value });

    const visible = fields.filter((f) => {
        if (!f.when) return true;
        return Object.entries(f.when).every(([k, v]) => values[k] === v);
    });

    return (
        <Form layout="vertical">
            {visible.map((f) => (
                <SectionFieldItem key={f.key} field={f} value={values[f.key]} onChange={(v) => set(f.key, v)} />
            ))}
        </Form>
    );
}

function SectionFieldItem({
    field,
    value,
    onChange,
}: {
    field: SectionFieldSpec;
    value: unknown;
    onChange: (v: unknown) => void;
}) {
    const label = field.label;

    switch (field.type) {
        case 'textarea':
            return (
                <Form.Item label={label} required={field.required}>
                    <Input.TextArea rows={3} value={(value as string) ?? ''} onChange={(e) => onChange(e.target.value)} />
                </Form.Item>
            );
        case 'richtext':
            return (
                <Form.Item label={label} required={field.required}>
                    <RichTextEditor value={(value as string) ?? ''} onChange={onChange} minHeight={160} />
                </Form.Item>
            );
        case 'boolean':
            return (
                <Form.Item label={label} valuePropName="checked">
                    <Switch checked={Boolean(value)} onChange={onChange} />
                </Form.Item>
            );
        case 'number':
            return (
                <Form.Item label={label}>
                    <InputNumber style={{ width: '100%' }} value={value as number} onChange={(v) => onChange(v)} />
                </Form.Item>
            );
        case 'select':
            return (
                <Form.Item label={label}>
                    <Select
                        allowClear
                        value={(value as string) ?? field.default ?? undefined}
                        options={Object.entries(field.options ?? {}).map(([v, l]) => ({ label: l, value: v }))}
                        onChange={(v) => onChange(v)}
                    />
                </Form.Item>
            );
        case 'media':
            return (
                <Form.Item label={label}>
                    <MediaField value={(value as number) ?? null} onChange={(id) => onChange(id)} />
                </Form.Item>
            );
        case 'media_multiple': {
            const ids = (value as number[]) ?? [];
            return (
                <Form.Item label={label}>
                    <Space wrap>
                        {ids.map((id, i) => (
                            <MediaField
                                key={id ?? i}
                                value={id}
                                onChange={(newId) =>
                                    onChange(ids.map((x, xi) => (xi === i ? newId : x)).filter(Boolean))
                                }
                            />
                        ))}
                        <MediaField value={null} onChange={(id) => id && onChange([...ids, id])} />
                    </Space>
                </Form.Item>
            );
        }
        case 'repeater': {
            const rows = (value as Record<string, unknown>[]) ?? [];
            return (
                <Form.Item label={label}>
                    {rows.map((row, i) => (
                        <Card key={i} size="small" style={{ marginBottom: 8 }}>
                            <SectionFields
                                fields={field.item ?? []}
                                values={row}
                                onChange={(v) => onChange(rows.map((r, ri) => (ri === i ? v : r)))}
                            />
                            <Button
                                size="small"
                                danger
                                icon={<DeleteOutlined />}
                                onClick={() => onChange(rows.filter((_, ri) => ri !== i))}
                            >
                                حذف
                            </Button>
                        </Card>
                    ))}
                    <Button type="dashed" block icon={<PlusOutlined />} onClick={() => onChange([...rows, {}])}>
                        افزودن ردیف
                    </Button>
                </Form.Item>
            );
        }
        case 'relation':
            // Free-form id list; the section editor keeps it simple — a comma list of slugs/ids.
            return (
                <Form.Item label={label} help="شناسه‌ها یا اسلاگ‌ها را با کاما جدا کنید (خالی = خودکار)">
                    <Input
                        dir="ltr"
                        value={Array.isArray(value) ? (value as string[]).join(',') : ''}
                        onChange={(e) =>
                            onChange(
                                e.target.value
                                    .split(',')
                                    .map((s) => s.trim())
                                    .filter(Boolean),
                            )
                        }
                    />
                </Form.Item>
            );
        default:
            return (
                <Form.Item label={label} required={field.required}>
                    <Input value={(value as string) ?? ''} onChange={(e) => onChange(e.target.value)} />
                </Form.Item>
            );
    }
}
