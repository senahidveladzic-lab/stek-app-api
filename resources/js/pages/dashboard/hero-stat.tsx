import { TrendingDown, TrendingUp, Minus } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { useTranslation } from '@/hooks/use-translation';

type HeroStatProps = {
    total: number;
    previousTotal: number;
    budget: number | null;
};

export function HeroStat({ total, previousTotal, budget }: HeroStatProps) {
    const { t, formatMoney } = useTranslation();

    const percentChange = previousTotal > 0
        ? Math.round(((total - previousTotal) / previousTotal) * 100)
        : null;

    const now = new Date();
    const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
    const timePercent = now.getDate() / daysInMonth;

    const hasBudget = budget !== null && budget > 0;
    const budgetPercent = hasBudget ? total / budget : 0;
    const budgetRemaining = hasBudget ? budget - total : 0;

    const spendPercent = hasBudget
        ? budgetPercent
        : previousTotal > 0 ? total / previousTotal : 0;

    const burnColor = hasBudget
        ? budgetPercent < timePercent * 0.9
            ? 'bg-emerald-500'
            : budgetPercent < timePercent * 1.15
                ? 'bg-amber-500'
                : budgetPercent >= 1
                    ? 'bg-red-500'
                    : 'bg-amber-500'
        : previousTotal === 0
            ? 'bg-primary'
            : (total / previousTotal) < timePercent * 0.9
                ? 'bg-emerald-500'
                : (total / previousTotal) < timePercent * 1.15
                    ? 'bg-amber-500'
                    : 'bg-red-500';

    return (
        <div className="from-primary/12 via-primary/4 to-card relative overflow-hidden rounded-xl border bg-gradient-to-br p-5 md:p-6">
            <div className="relative z-10">
                <p className="text-muted-foreground mb-1 text-xs font-medium uppercase tracking-wide">
                    {t('dashboard.spending_this_month')}
                </p>
                <div className="flex items-baseline gap-2">
                    <p className="text-3xl font-bold tracking-tight tabular-nums md:text-4xl">
                        {formatMoney(total)}
                    </p>
                    {hasBudget && (
                        <p className="text-muted-foreground text-base font-medium tabular-nums">
                            / {formatMoney(budget)}
                        </p>
                    )}
                </div>
                <div className="mt-2.5 flex flex-wrap items-center gap-2">
                    {hasBudget && (
                        <Badge variant={budgetRemaining >= 0 ? 'secondary' : 'destructive'} className="gap-1">
                            {budgetRemaining >= 0
                                ? t('budget.remaining', { amount: formatMoney(budgetRemaining) })
                                : t('budget.over', { amount: formatMoney(Math.abs(budgetRemaining)) })
                            }
                        </Badge>
                    )}
                    {percentChange !== null && (
                        <>
                            <Badge variant={percentChange <= 0 ? 'secondary' : 'destructive'} className="gap-1">
                                {percentChange < 0 ? (
                                    <TrendingDown className="h-3 w-3" />
                                ) : percentChange > 0 ? (
                                    <TrendingUp className="h-3 w-3" />
                                ) : (
                                    <Minus className="h-3 w-3" />
                                )}
                                {percentChange > 0 ? '+' : ''}{percentChange}%
                            </Badge>
                            <span className="text-muted-foreground text-xs">
                                {t('dashboard.vs_last_month')}
                            </span>
                        </>
                    )}
                </div>
                {(hasBudget || previousTotal > 0) && (
                    <div className="mt-3.5">
                        <div className="text-muted-foreground mb-1 flex justify-between text-[10px] font-medium">
                            <span>
                                {hasBudget
                                    ? t('budget.used', { percent: Math.round(budgetPercent * 100) })
                                    : t('dashboard.burn_rate')
                                }
                            </span>
                            <span>{t('dashboard.percent_of_month', { percent: Math.round(timePercent * 100) })}</span>
                        </div>
                        <div className="bg-primary/10 relative h-2 w-full overflow-hidden rounded-full">
                            <div
                                className={`h-full rounded-full transition-all ${burnColor}`}
                                style={{ width: `${Math.min(spendPercent * 100, 100)}%` }}
                            />
                            <div
                                className="bg-foreground/30 absolute top-0 h-full w-0.5 rounded-full"
                                style={{ left: `${timePercent * 100}%` }}
                            />
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
