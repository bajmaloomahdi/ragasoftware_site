import { router, usePage } from '@inertiajs/react';
import { Layout, Button, Dropdown, Avatar, Space, Typography } from 'antd';
import { MenuFoldOutlined, MenuUnfoldOutlined, LogoutOutlined, UserOutlined, ExportOutlined } from '@ant-design/icons';
import type { PageProps } from '@/types/global';

const { Header } = Layout;

export default function AdminHeader({
    isMobile,
    collapsed,
    onToggle,
}: {
    isMobile: boolean;
    collapsed: boolean;
    onToggle: () => void;
}) {
    const { props } = usePage<PageProps>();
    const user = props.auth?.user;

    return (
        <Header
            style={{
                position: 'sticky',
                top: 0,
                zIndex: 10,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                padding: '0 16px',
                background: '#fff',
                boxShadow: '0 1px 4px rgba(0,0,0,0.06)',
            }}
        >
            <Button
                type="text"
                aria-label="باز/بستن منو"
                icon={collapsed || isMobile ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
                onClick={onToggle}
            />

            <Space size="large">
                <a href="/" target="_blank" rel="noreferrer">
                    <Space size={4}>
                        مشاهده سایت <ExportOutlined />
                    </Space>
                </a>
                <Dropdown
                    menu={{
                        items: [
                            {
                                key: 'logout',
                                icon: <LogoutOutlined />,
                                label: 'خروج',
                                onClick: () => router.post(route('logout')),
                            },
                        ],
                    }}
                >
                    <Space style={{ cursor: 'pointer' }}>
                        <Avatar size="small" icon={<UserOutlined />} />
                        <Typography.Text>{user?.name ?? 'کاربر'}</Typography.Text>
                    </Space>
                </Dropdown>
            </Space>
        </Header>
    );
}
