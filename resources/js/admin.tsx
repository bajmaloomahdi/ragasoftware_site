import '../css/admin.css';

import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ConfigProvider, App as AntApp } from 'antd';
import faIR from 'antd/locale/fa_IR';
import dayjs from 'dayjs';
import 'dayjs/locale/fa';
import { adminTheme } from './theme';

dayjs.locale('fa');

const appName = import.meta.env.VITE_APP_NAME || 'RagaSoftware';

createInertiaApp({
    title: (title) => (title ? `${title} | مدیریت ${appName}` : `مدیریت ${appName}`),
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.tsx`, import.meta.glob('./Pages/**/*.tsx')),
    setup({ el, App, props }) {
        createRoot(el).render(
            <React.StrictMode>
                <ConfigProvider direction="rtl" locale={faIR} theme={adminTheme}>
                    <AntApp>
                        <App {...props} />
                    </AntApp>
                </ConfigProvider>
            </React.StrictMode>,
        );
    },
    progress: { color: '#2563eb' },
});
