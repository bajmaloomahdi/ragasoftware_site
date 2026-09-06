import { Form, Input, InputNumber, Select, Switch } from 'antd';
import RichTextEditor from './RichTextEditor';
import MediaField from './MediaField';

export interface FieldSpec {
    key: string;
    type:
        | 'text'
        | 'textarea'
        | 'richtext'
        | 'number'
        | 'switch'
        | 'select'
        | 'multiselect'
        | 'media'
        | 'slug'
        | 'url'
        | 'email';
    label: string;
    required?: boolean;
    placeholder?: string;
    help?: string;
    options?: { label: string; value: string | number }[];
    rows?: number;
    span?: number;
    /** show only when another field equals a value */
    when?: [string, unknown];
}

interface Props {
    field: FieldSpec;
    value: unknown;
    error?: string;
    onChange: (value: unknown) => void;
}

export default function FieldRenderer({ field, value, error, onChange }: Props) {
    const common = {
        label: field.label,
        required: field.required,
        validateStatus: error ? ('error' as const) : undefined,
        help: error ?? field.help,
    };

    switch (field.type) {
        case 'textarea':
            return (
                <Form.Item {...common}>
                    <Input.TextArea
                        rows={field.rows ?? 3}
                        value={(value as string) ?? ''}
                        placeholder={field.placeholder}
                        onChange={(e) => onChange(e.target.value)}
                    />
                </Form.Item>
            );
        case 'richtext':
            return (
                <Form.Item {...common}>
                    <RichTextEditor value={(value as string) ?? ''} onChange={onChange} />
                </Form.Item>
            );
        case 'number':
            return (
                <Form.Item {...common}>
                    <InputNumber
                        style={{ width: '100%' }}
                        value={value as number}
                        onChange={(v) => onChange(v ?? 0)}
                    />
                </Form.Item>
            );
        case 'switch':
            return (
                <Form.Item {...common} valuePropName="checked">
                    <Switch checked={Boolean(value)} onChange={onChange} />
                </Form.Item>
            );
        case 'select':
            return (
                <Form.Item {...common}>
                    <Select
                        allowClear
                        showSearch
                        optionFilterProp="label"
                        value={(value as string | number) ?? undefined}
                        options={field.options}
                        placeholder={field.placeholder}
                        onChange={(v) => onChange(v ?? null)}
                    />
                </Form.Item>
            );
        case 'multiselect':
            return (
                <Form.Item {...common}>
                    <Select
                        mode="multiple"
                        allowClear
                        optionFilterProp="label"
                        value={(value as (string | number)[]) ?? []}
                        options={field.options}
                        placeholder={field.placeholder}
                        onChange={(v) => onChange(v)}
                    />
                </Form.Item>
            );
        case 'media':
            return (
                <Form.Item {...common}>
                    <MediaField value={(value as number) ?? null} onChange={(id) => onChange(id)} />
                </Form.Item>
            );
        default:
            return (
                <Form.Item {...common}>
                    <Input
                        type={field.type === 'email' ? 'email' : 'text'}
                        dir={field.type === 'url' || field.type === 'email' || field.type === 'slug' ? 'ltr' : undefined}
                        value={(value as string) ?? ''}
                        placeholder={field.placeholder}
                        onChange={(e) => onChange(e.target.value)}
                    />
                </Form.Item>
            );
    }
}
