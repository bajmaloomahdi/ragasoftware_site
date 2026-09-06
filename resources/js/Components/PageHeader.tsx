import type { ReactNode } from 'react';
import { Typography, Space, Flex } from 'antd';

interface Props {
    title: string;
    subtitle?: string;
    icon?: ReactNode;
    actions?: ReactNode;
}

export default function PageHeader({ title, subtitle, icon, actions }: Props) {
    return (
        <Flex
            align="center"
            justify="space-between"
            wrap
            gap={12}
            style={{ marginBottom: 20 }}
        >
            <Space size={12} align="center">
                {icon && <span style={{ fontSize: 22, color: '#2563eb' }}>{icon}</span>}
                <div>
                    <Typography.Title level={4} style={{ margin: 0 }}>
                        {title}
                    </Typography.Title>
                    {subtitle && (
                        <Typography.Text type="secondary">{subtitle}</Typography.Text>
                    )}
                </div>
            </Space>
            {actions && <Space wrap>{actions}</Space>}
        </Flex>
    );
}
