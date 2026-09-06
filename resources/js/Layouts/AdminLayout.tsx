import { PropsWithChildren, useState } from 'react';
import { Layout, Grid } from 'antd';
import AdminSidebar from '@/Components/AdminSidebar';
import AdminHeader from '@/Components/AdminHeader';
import FlashListener from '@/Components/FlashListener';

const { Content } = Layout;

export default function AdminLayout({ children }: PropsWithChildren) {
    const screens = Grid.useBreakpoint();
    const isMobile = !screens.lg;
    const [collapsed, setCollapsed] = useState(false);
    const [drawerOpen, setDrawerOpen] = useState(false);

    return (
        <Layout style={{ minHeight: '100vh' }} hasSider>
            <AdminSidebar
                isMobile={isMobile}
                collapsed={collapsed}
                drawerOpen={drawerOpen}
                onCloseDrawer={() => setDrawerOpen(false)}
            />
            <Layout>
                <AdminHeader
                    isMobile={isMobile}
                    collapsed={collapsed}
                    onToggle={() =>
                        isMobile ? setDrawerOpen((v) => !v) : setCollapsed((v) => !v)
                    }
                />
                <Content style={{ margin: 16, padding: 24, background: '#fff', borderRadius: 12 }}>
                    <FlashListener />
                    {children}
                </Content>
            </Layout>
        </Layout>
    );
}
