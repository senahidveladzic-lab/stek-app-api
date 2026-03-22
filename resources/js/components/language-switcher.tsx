import { router, usePage } from '@inertiajs/react';
import type { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

const localeOptions: { value: string; label: string; flag: string }[] = [
    { value: 'bs', label: 'Bosanski', flag: '🇧🇦' },
    { value: 'en', label: 'English', flag: '🇬🇧' },
];

export function LanguageSwitcher({ className = '', ...props }: HTMLAttributes<HTMLDivElement>) {
    const { locale } = usePage().props;

    function handleChange(value: string) {
        router.patch('/settings/locale', { locale: value }, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    return (
        <div
            className={cn(
                'inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800',
                className,
            )}
            {...props}
        >
            {localeOptions.map(({ value, label, flag }) => (
                <button
                    key={value}
                    onClick={() => handleChange(value)}
                    className={cn(
                        'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                        locale === value
                            ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                            : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
                    )}
                >
                    <span className="-ml-1 text-base">{flag}</span>
                    <span className="ml-1.5 text-sm">{label}</span>
                </button>
            ))}
        </div>
    );
}
