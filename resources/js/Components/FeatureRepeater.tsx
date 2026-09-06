import { Button, Card, Input, Space } from 'antd';
import { PlusOutlined, DeleteOutlined, ArrowUpOutlined, ArrowDownOutlined } from '@ant-design/icons';

export interface FeatureRow {
    id?: number | null;
    title: string;
    description?: string;
    icon?: string;
    media_id?: number | null;
}

interface Props {
    value: FeatureRow[];
    onChange: (rows: FeatureRow[]) => void;
    withIcon?: boolean;
}

/** Simple add/remove/reorder list of {title, description, icon} rows. */
export default function FeatureRepeater({ value, onChange, withIcon = true }: Props) {
    const rows = value ?? [];
    const update = (i: number, patch: Partial<FeatureRow>) =>
        onChange(rows.map((r, idx) => (idx === i ? { ...r, ...patch } : r)));
    const remove = (i: number) => onChange(rows.filter((_, idx) => idx !== i));
    const move = (i: number, dir: -1 | 1) => {
        const j = i + dir;
        if (j < 0 || j >= rows.length) return;
        const next = [...rows];
        [next[i], next[j]] = [next[j], next[i]];
        onChange(next);
    };

    return (
        <div>
            {rows.map((row, i) => (
                <Card key={i} size="small" style={{ marginBottom: 8 }} styles={{ body: { padding: 12 } }}>
                    <Space align="start" style={{ width: '100%' }} direction="vertical" size={8}>
                        <Space wrap style={{ width: '100%' }}>
                            <Input
                                placeholder="عنوان"
                                style={{ width: 240 }}
                                value={row.title}
                                onChange={(e) => update(i, { title: e.target.value })}
                            />
                            {withIcon && (
                                <Input
                                    placeholder="آیکن (اختیاری)"
                                    style={{ width: 150 }}
                                    value={row.icon ?? ''}
                                    onChange={(e) => update(i, { icon: e.target.value })}
                                />
                            )}
                            <Button size="small" icon={<ArrowUpOutlined />} onClick={() => move(i, -1)} />
                            <Button size="small" icon={<ArrowDownOutlined />} onClick={() => move(i, 1)} />
                            <Button size="small" danger icon={<DeleteOutlined />} onClick={() => remove(i)} />
                        </Space>
                        <Input.TextArea
                            placeholder="توضیح"
                            rows={2}
                            value={row.description ?? ''}
                            onChange={(e) => update(i, { description: e.target.value })}
                        />
                    </Space>
                </Card>
            ))}
            <Button
                type="dashed"
                block
                icon={<PlusOutlined />}
                onClick={() => onChange([...rows, { title: '', description: '', icon: '' }])}
            >
                افزودن مورد
            </Button>
        </div>
    );
}
