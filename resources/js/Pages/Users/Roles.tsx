import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Card, Checkbox, Collapse, Button, Space, Tag, Modal, Input, App, Popconfirm } from 'antd';
import { SafetyCertificateOutlined, PlusOutlined, DeleteOutlined, SaveOutlined } from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';
import type { PageProps } from '@/types/global';

interface Role {
    id: number;
    name: string;
    permissions: string[];
    locked: boolean;
}
type Group = { label: string; permissions: Record<string, string> };

type Props = PageProps<{ roles: Role[]; groups: Record<string, Group> }>;

export default function RolesPage({ roles, groups }: Props) {
    const { message } = App.useApp();
    const [draft, setDraft] = useState<Record<number, Set<string>>>(
        Object.fromEntries(roles.map((r) => [r.id, new Set(r.permissions)])),
    );
    const [newRoleOpen, setNewRoleOpen] = useState(false);
    const [newRole, setNewRole] = useState('');

    const allPerms = Object.values(groups).flatMap((g) => Object.keys(g.permissions));

    const toggle = (roleId: number, perm: string) =>
        setDraft((d) => {
            const set = new Set(d[roleId]);
            set.has(perm) ? set.delete(perm) : set.add(perm);
            return { ...d, [roleId]: set };
        });

    const toggleGroup = (roleId: number, perms: string[], on: boolean) =>
        setDraft((d) => {
            const set = new Set(d[roleId]);
            perms.forEach((p) => (on ? set.add(p) : set.delete(p)));
            return { ...d, [roleId]: set };
        });

    const save = (role: Role) =>
        router.put(
            route('admin.roles.update', role.id),
            { permissions: [...draft[role.id]] },
            { preserveScroll: true, onSuccess: () => message.success('ذخیره شد') },
        );

    return (
        <AdminLayout>
            <Head title="نقش‌ها و دسترسی‌ها" />
            <PageHeader
                title="نقش‌ها و دسترسی‌ها"
                subtitle="کنترل دسترسی بر اساس نقش"
                icon={<SafetyCertificateOutlined />}
                actions={
                    <Button type="primary" icon={<PlusOutlined />} onClick={() => setNewRoleOpen(true)}>
                        نقش جدید
                    </Button>
                }
            />

            <Space direction="vertical" style={{ width: '100%' }} size={16}>
                {roles.map((role) => (
                    <Card
                        key={role.id}
                        title={
                            <Space>
                                {role.name}
                                {role.locked && <Tag color="red">قفل‌شده</Tag>}
                            </Space>
                        }
                        extra={
                            !role.locked && (
                                <Space>
                                    <Button type="primary" icon={<SaveOutlined />} onClick={() => save(role)}>
                                        ذخیره
                                    </Button>
                                    {!['admin'].includes(role.name) && (
                                        <Popconfirm
                                            title="حذف این نقش؟"
                                            okText="حذف"
                                            cancelText="انصراف"
                                            onConfirm={() => router.delete(route('admin.roles.destroy', role.id))}
                                        >
                                            <Button danger icon={<DeleteOutlined />} />
                                        </Popconfirm>
                                    )}
                                </Space>
                            )
                        }
                    >
                        {role.locked ? (
                            <Tag>دسترسی کامل به همهٔ بخش‌ها</Tag>
                        ) : (
                            <Collapse
                                items={Object.entries(groups).map(([key, group]) => {
                                    const perms = Object.keys(group.permissions);
                                    const checkedCount = perms.filter((p) => draft[role.id].has(p)).length;
                                    return {
                                        key,
                                        label: (
                                            <Space>
                                                {group.label}
                                                <Tag>{checkedCount}/{perms.length}</Tag>
                                            </Space>
                                        ),
                                        extra: (
                                            <Checkbox
                                                checked={checkedCount === perms.length}
                                                indeterminate={checkedCount > 0 && checkedCount < perms.length}
                                                onClick={(e) => e.stopPropagation()}
                                                onChange={(e) => toggleGroup(role.id, perms, e.target.checked)}
                                            />
                                        ),
                                        children: (
                                            <Space direction="vertical">
                                                {Object.entries(group.permissions).map(([perm, label]) => (
                                                    <Checkbox
                                                        key={perm}
                                                        checked={draft[role.id].has(perm)}
                                                        onChange={() => toggle(role.id, perm)}
                                                    >
                                                        {label} <code style={{ color: '#98a2b3' }}>{perm}</code>
                                                    </Checkbox>
                                                ))}
                                            </Space>
                                        ),
                                    };
                                })}
                            />
                        )}
                    </Card>
                ))}
            </Space>

            <Modal
                open={newRoleOpen}
                title="نقش جدید"
                okText="ساخت"
                cancelText="انصراف"
                onCancel={() => setNewRoleOpen(false)}
                onOk={() =>
                    router.post(route('admin.roles.store'), { name: newRole, permissions: [] }, {
                        onSuccess: () => {
                            setNewRoleOpen(false);
                            setNewRole('');
                        },
                    })
                }
            >
                <Input placeholder="نام نقش (انگلیسی، مثلاً content-manager)" value={newRole} onChange={(e) => setNewRole(e.target.value)} />
            </Modal>
        </AdminLayout>
    );
}
