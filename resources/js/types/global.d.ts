/// <reference types="vite/client" />

import type { PageProps as InertiaPageProps } from '@inertiajs/core';
import type { route as ziggyRoute } from 'ziggy-js';

declare global {
    /* Ziggy's helper, injected by the @routes Blade directive */
    const route: typeof ziggyRoute;
    interface Window {
        route: typeof ziggyRoute;
        Alpine: unknown;
    }
}

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    roles: string[];
    permissions: string[];
}

export interface SharedProps {
    appName: string;
    auth: { user: AuthUser | null };
    flash: {
        success: string | null;
        error: string | null;
        info: string | null;
    };
    ziggy: Record<string, unknown> & { location: string };
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> =
    InertiaPageProps & SharedProps & T;
