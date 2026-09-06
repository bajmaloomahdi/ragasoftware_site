import { useState } from 'react';
import {
    DndContext, closestCenter, PointerSensor, useSensor, useSensors, type DragEndEvent,
} from '@dnd-kit/core';
import {
    SortableContext, verticalListSortingStrategy, useSortable, arrayMove,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { Button, Card, Collapse, Dropdown, Empty, Input, Space, Switch, Tag } from 'antd';
import { HolderOutlined, DeleteOutlined, PlusOutlined } from '@ant-design/icons';
import SectionFields, { type SectionFieldSpec } from './SectionFields';

export interface SectionType {
    type: string;
    label: string;
    icon: string;
    fields: SectionFieldSpec[];
}

export interface SectionRow {
    key: string; // stable client key
    id?: number | null;
    type: string;
    name: string;
    settings: Record<string, unknown>;
    is_active: boolean;
}

interface Props {
    value: SectionRow[];
    types: SectionType[];
    onChange: (rows: SectionRow[]) => void;
}

let counter = 0;
const newKey = () => `s-${Date.now()}-${counter++}`;

export default function SectionBuilder({ value, types, onChange }: Props) {
    const rows = value ?? [];
    const [activeKeys, setActiveKeys] = useState<string[]>([]);
    const sensors = useSensors(useSensor(PointerSensor, { activationConstraint: { distance: 5 } }));
    const typeMap = Object.fromEntries(types.map((t) => [t.type, t]));

    const onDragEnd = ({ active, over }: DragEndEvent) => {
        if (!over || active.id === over.id) return;
        const from = rows.findIndex((r) => r.key === active.id);
        const to = rows.findIndex((r) => r.key === over.id);
        onChange(arrayMove(rows, from, to));
    };

    const update = (key: string, patch: Partial<SectionRow>) =>
        onChange(rows.map((r) => (r.key === key ? { ...r, ...patch } : r)));

    const add = (type: string) => {
        const key = newKey();
        onChange([
            ...rows,
            { key, type, name: typeMap[type]?.label ?? type, settings: {}, is_active: true },
        ]);
        setActiveKeys((k) => [...k, key]);
    };

    return (
        <div>
            <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={onDragEnd}>
                <SortableContext items={rows.map((r) => r.key)} strategy={verticalListSortingStrategy}>
                    {rows.length === 0 && <Empty description="هنوز بخشی اضافه نشده است" />}
                    {rows.map((row) => (
                        <SortableSection
                            key={row.key}
                            row={row}
                            def={typeMap[row.type]}
                            expanded={activeKeys.includes(row.key)}
                            onToggleExpand={() =>
                                setActiveKeys((k) =>
                                    k.includes(row.key) ? k.filter((x) => x !== row.key) : [...k, row.key],
                                )
                            }
                            onUpdate={(patch) => update(row.key, patch)}
                            onDelete={() => onChange(rows.filter((r) => r.key !== row.key))}
                        />
                    ))}
                </SortableContext>
            </DndContext>

            <Dropdown
                trigger={['click']}
                menu={{ items: types.map((t) => ({ key: t.type, label: t.label, onClick: () => add(t.type) })) }}
            >
                <Button type="dashed" block icon={<PlusOutlined />} style={{ marginTop: 12 }}>
                    افزودن بخش
                </Button>
            </Dropdown>
        </div>
    );
}

function SortableSection({
    row, def, expanded, onToggleExpand, onUpdate, onDelete,
}: {
    row: SectionRow;
    def?: SectionType;
    expanded: boolean;
    onToggleExpand: () => void;
    onUpdate: (patch: Partial<SectionRow>) => void;
    onDelete: () => void;
}) {
    const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({ id: row.key });

    return (
        <div
            ref={setNodeRef}
            style={{ transform: CSS.Transform.toString(transform), transition, opacity: isDragging ? 0.5 : 1, marginBottom: 8 }}
        >
            <Card size="small" styles={{ body: { padding: 0 } }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '8px 12px' }}>
                    <span {...attributes} {...listeners} style={{ cursor: 'grab', color: '#98a2b3' }}>
                        <HolderOutlined />
                    </span>
                    <Tag>{def?.label ?? row.type}</Tag>
                    <Input
                        size="small"
                        variant="borderless"
                        value={row.name}
                        placeholder="نام بخش"
                        style={{ flex: 1 }}
                        onChange={(e) => onUpdate({ name: e.target.value })}
                    />
                    <Space size={4}>
                        <Switch
                            size="small"
                            checked={row.is_active}
                            onChange={(v) => onUpdate({ is_active: v })}
                            checkedChildren="فعال"
                            unCheckedChildren="غیرفعال"
                        />
                        <Button size="small" type="text" onClick={onToggleExpand}>
                            {expanded ? 'بستن' : 'ویرایش'}
                        </Button>
                        <Button size="small" type="text" danger icon={<DeleteOutlined />} onClick={onDelete} />
                    </Space>
                </div>

                {expanded && (
                    <div style={{ padding: 12, borderTop: '1px solid #f0f0f0', background: '#fafafa' }}>
                        {def ? (
                            <SectionFields
                                fields={def.fields}
                                values={row.settings}
                                onChange={(settings) => onUpdate({ settings })}
                            />
                        ) : (
                            <Collapse ghost items={[{ key: '1', label: 'این نوع بخش شناخته نشد', children: null }]} />
                        )}
                    </div>
                )}
            </Card>
        </div>
    );
}
