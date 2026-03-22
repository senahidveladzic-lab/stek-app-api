import { usePage } from '@inertiajs/react';

export function useTranslation() {
    const { translations, formats, locale } = usePage().props;

    function t(key: string, replacements?: Record<string, string | number>): string {
        let value = translations[key] ?? key;

        if (replacements) {
            for (const [placeholder, replacement] of Object.entries(replacements)) {
                value = value.replace(new RegExp(`:${placeholder}`, 'g'), String(replacement));
            }
        }

        return value;
    }

    function formatMoney(amount: number, currencyOverride?: string): string {
        const parts = amount.toFixed(2).split('.');
        const integerPart = parts[0].replace(
            /\B(?=(\d{3})+(?!\d))/g,
            formats.thousands_separator,
        );
        const formatted = integerPart + formats.decimal_separator + parts[1];
        const symbol = currencyOverride ?? formats.currency_symbol;

        return formats.currency_position === 'before'
            ? `${symbol} ${formatted}`
            : `${formatted} ${symbol}`;
    }

    function formatDate(dateString: string): string {
        const datePart = dateString.includes('T') ? dateString.split('T')[0] : dateString;
        const date = new Date(datePart + 'T00:00:00');
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
        ];

        return formats.date
            .replace('d', day)
            .replace('m', month)
            .replace('Y', String(year))
            .replace('M', monthNames[date.getMonth()]);
    }

    return { t, formatMoney, formatDate, locale, formats };
}
