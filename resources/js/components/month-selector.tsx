import { format, startOfMonth, endOfMonth, addMonths, subMonths } from 'date-fns';
import { bs, enUS } from 'date-fns/locale';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useTranslation } from '@/hooks/use-translation';

type MonthSelectorProps = {
    from?: string;
    to?: string;
    onChange: (from: string, to: string) => void;
};

function toDateString(date: Date): string {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function parseCurrentMonth(from?: string): Date {
    if (from) {
        const parsed = new Date(from + 'T00:00:00');
        if (!isNaN(parsed.getTime())) {
            return startOfMonth(parsed);
        }
    }
    return startOfMonth(new Date());
}

/**
 * Month navigation with prev/next arrows, matching the Expo app pattern.
 */
export function MonthSelector({ from, onChange }: MonthSelectorProps) {
    const { locale } = useTranslation();
    const dateFnsLocale = locale === 'bs' ? bs : enUS;
    const current = parseCurrentMonth(from);

    function navigate(direction: 'prev' | 'next') {
        const next = direction === 'prev' ? subMonths(current, 1) : addMonths(current, 1);
        onChange(toDateString(startOfMonth(next)), toDateString(endOfMonth(next)));
    }

    const label = format(current, 'LLLL yyyy', { locale: dateFnsLocale });
    const capitalizedLabel = label.charAt(0).toUpperCase() + label.slice(1);

    return (
        <div className="flex items-center gap-1">
            <Button variant="outline" size="icon" className="h-9 w-9" onClick={() => navigate('prev')}>
                <ChevronLeft className="h-4 w-4" />
            </Button>
            <span className="w-[140px] text-center text-sm font-medium">
                {capitalizedLabel}
            </span>
            <Button variant="outline" size="icon" className="h-9 w-9" onClick={() => navigate('next')}>
                <ChevronRight className="h-4 w-4" />
            </Button>
        </div>
    );
}
