import { createContext, useContext, useLayoutEffect, useRef, useState } from 'react';
import type { ReactNode } from 'react';
import type { BreadcrumbItem } from '@/types';

interface BreadcrumbsContextValue {
    breadcrumbs: BreadcrumbItem[];
    setBreadcrumbs: (items: BreadcrumbItem[]) => void;
}

const BreadcrumbsContext = createContext<BreadcrumbsContextValue>({
    breadcrumbs: [],
    setBreadcrumbs: () => {},
});

export function BreadcrumbsProvider({ children }: { children: ReactNode }) {
    const [breadcrumbs, setBreadcrumbs] = useState<BreadcrumbItem[]>([]);

    return (
        <BreadcrumbsContext.Provider value={{ breadcrumbs, setBreadcrumbs }}>
            {children}
        </BreadcrumbsContext.Provider>
    );
}

/** Call inside a page component to set the breadcrumbs for that page. */
export function useBreadcrumbs(items: BreadcrumbItem[]) {
    const { setBreadcrumbs } = useContext(BreadcrumbsContext);
    const ref = useRef(items);

    useLayoutEffect(() => {
        setBreadcrumbs(ref.current);
        return () => setBreadcrumbs([]);
        // items are static per page — intentionally run once on mount
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);
}

/** Read the current breadcrumbs — use inside the layout. */
export function useBreadcrumbItems(): BreadcrumbItem[] {
    return useContext(BreadcrumbsContext).breadcrumbs;
}
