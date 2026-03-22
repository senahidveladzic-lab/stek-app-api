import { ArrowDown, ArrowUp, Equal } from 'lucide-react';
import { useTranslation } from '@/hooks/use-translation';

type MonthlyComparisonProps = {
    totalThisMonth: number;
    previousMonthSamePeriodTotal: number;
};

export function MonthlyComparison({ totalThisMonth, previousMonthSamePeriodTotal }: MonthlyComparisonProps) {
    const { t, formatMoney } = useTranslation();

    const diff = totalThisMonth - previousMonthSamePeriodTotal;
    const absDiff = Math.abs(diff);

    const iconColor = diff > 0 ? 'text-red-500' : diff < 0 ? 'text-emerald-500' : 'text-muted-foreground';
    const iconBgColor = diff > 0 ? 'bg-red-500/10' : diff < 0 ? 'bg-emerald-500/10' : 'bg-muted';
    const gradientFrom = diff > 0 ? 'from-red-500/8' : diff < 0 ? 'from-emerald-500/8' : 'from-muted/50';
    const gradientVia = diff > 0 ? 'via-red-500/3' : diff < 0 ? 'via-emerald-500/3' : 'via-muted/20';

    return (
        <div className={`${gradientFrom} ${gradientVia} to-card relative overflow-hidden rounded-xl border bg-gradient-to-br p-5 md:p-6`}>
            <p className="text-muted-foreground mb-4 text-xs font-medium uppercase tracking-wide">
                {t('dashboard.month_comparison')}
            </p>
            <div className="flex items-start gap-3">
                <div className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${iconBgColor}`}>
                    {diff > 0 ? (
                        <ArrowUp className={`h-5 w-5 ${iconColor}`} />
                    ) : diff < 0 ? (
                        <ArrowDown className={`h-5 w-5 ${iconColor}`} />
                    ) : (
                        <Equal className={`h-5 w-5 ${iconColor}`} />
                    )}
                </div>
                <div className="min-w-0">
                    <p className="text-sm font-medium leading-snug">
                        {diff > 0
                            ? t('dashboard.thats_more', { amount: formatMoney(absDiff) })
                            : diff < 0
                                ? t('dashboard.thats_less', { amount: formatMoney(absDiff) })
                                : t('dashboard.thats_same')}
                    </p>
                    <p className="text-muted-foreground mt-1 text-xs">
                        {t('dashboard.vs_last_month')}
                    </p>
                </div>
            </div>
        </div>
    );
}
