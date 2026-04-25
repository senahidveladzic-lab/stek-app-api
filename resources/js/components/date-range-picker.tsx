import { format } from 'date-fns';
import { bs, enUS } from 'date-fns/locale';
import { CalendarIcon } from 'lucide-react';
import type { DateRange } from 'react-day-picker';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { useTranslation } from '@/hooks/use-translation';
import { cn } from '@/lib/utils';

type DateRangePickerProps = {
    from?: string;
    to?: string;
    onChange: (from: string, to: string) => void;
};

/**
 * Date range picker with calendar popover.
 * Accepts and emits dates as YYYY-MM-DD strings.
 */
export function DateRangePicker({ from, to, onChange }: DateRangePickerProps) {
    const { t, locale } = useTranslation();
    const dateFnsLocale = locale === 'bs' ? bs : enUS;

    const selected: DateRange | undefined =
        from || to
            ? {
                  from: from ? new Date(from + 'T00:00:00') : undefined,
                  to: to ? new Date(to + 'T00:00:00') : undefined,
              }
            : undefined;

    function toDateString(date: Date): string {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function handleSelect(range: DateRange | undefined) {
        const newFrom = range?.from ? toDateString(range.from) : '';
        const newTo = range?.to ? toDateString(range.to) : '';
        onChange(newFrom, newTo);
    }

    function formatLabel(): string {
        if (selected?.from) {
            const fromStr = format(selected.from, 'dd. MMM yyyy', { locale: dateFnsLocale });
            if (selected.to) {
                const toStr = format(selected.to, 'dd. MMM yyyy', { locale: dateFnsLocale });
                return `${fromStr} - ${toStr}`;
            }
            return fromStr;
        }
        return `${t('common.from')} - ${t('common.to')}`;
    }

    return (
        <Popover>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    className={cn(
                        'w-[280px] justify-start text-left font-normal',
                        !selected && 'text-muted-foreground',
                    )}
                >
                    <CalendarIcon className="mr-2 h-4 w-4" />
                    {formatLabel()}
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0" align="start">
                <Calendar
                    mode="range"
                    selected={selected}
                    onSelect={handleSelect}
                    numberOfMonths={2}
                    locale={dateFnsLocale}
                />
            </PopoverContent>
        </Popover>
    );
}
