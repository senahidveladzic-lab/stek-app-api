import type { ReactNode } from 'react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';

/** Persistent layout function — assign to `Page.layout` on each page component. */
export default function appLayout(page: ReactNode): ReactNode {
    return <AppSidebarLayout>{page}</AppSidebarLayout>;
}
