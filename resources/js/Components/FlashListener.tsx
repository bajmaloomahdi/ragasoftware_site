import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { App } from 'antd';
import type { PageProps } from '@/types/global';

/** Surfaces Laravel session flash messages as Ant Design notifications. */
export default function FlashListener() {
    const { props } = usePage<PageProps>();
    const { message } = App.useApp();
    const flash = props.flash;

    useEffect(() => {
        if (flash?.success) message.success(flash.success);
        if (flash?.error) message.error(flash.error);
        if (flash?.info) message.info(flash.info);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [flash?.success, flash?.error, flash?.info]);

    return null;
}
