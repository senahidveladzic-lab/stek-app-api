import type { Auth } from '@/types/auth';
import type { Household } from '@/types/household';
import type { LocaleFormats } from '@/types/i18n';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            household: Household | null;
            sidebarOpen: boolean;
            locale: string;
            availableLocales: string[];
            translations: Record<string, string>;
            formats: LocaleFormats;
            flash: {
                success: string | null;
                error: string | null;
            };
            [key: string]: unknown;
        };
    }
}

declare global {
    interface Window {
        Paddle?: {
            Checkout: {
                open: (options: Record<string, unknown>) => void;
            };
        };
    }
}
