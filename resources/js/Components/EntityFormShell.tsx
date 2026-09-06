import type { ReactNode } from 'react';
import { Link } from '@inertiajs/react';
import { Button, Col, Row, Space, Card } from 'antd';
import { ArrowRightOutlined, SaveOutlined } from '@ant-design/icons';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/PageHeader';

interface Props {
    title: string;
    backRoute: string;
    icon?: ReactNode;
    processing: boolean;
    onSave: () => void;
    main: ReactNode;
    side: ReactNode;
    headerExtra?: ReactNode;
    children?: ReactNode;
}

/** Two-column edit layout shared by product / service / project / post forms. */
export default function EntityFormShell({
    title, backRoute, icon, processing, onSave, main, side, headerExtra, children,
}: Props) {
    return (
        <AdminLayout>
            {children}
            <PageHeader
                title={title}
                icon={icon}
                actions={
                    <Space>
                        {headerExtra}
                        <Link href={backRoute}>
                            <Button icon={<ArrowRightOutlined />}>بازگشت</Button>
                        </Link>
                        <Button type="primary" icon={<SaveOutlined />} loading={processing} onClick={onSave}>
                            ذخیره
                        </Button>
                    </Space>
                }
            />
            <Row gutter={16}>
                <Col xs={24} lg={16}>
                    {main}
                </Col>
                <Col xs={24} lg={8}>
                    <Card>{side}</Card>
                </Col>
            </Row>
        </AdminLayout>
    );
}
