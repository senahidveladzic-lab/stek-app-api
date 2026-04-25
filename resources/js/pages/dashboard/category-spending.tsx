import { ChevronDown, ChevronUp } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslation } from '@/hooks/use-translation';
import type { CategoryData } from './types';

const MAX_VISIBLE = 6;

type CategorySpendingProps = {
    data: CategoryData[];
};

export function CategorySpending({ data }: CategorySpendingProps) {
    const { t, formatMoney } = useTranslation();
    const [showAll, setShowAll] = useState(false);

    if (data.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>{t('dashboard.by_category')}</CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-muted-foreground py-8 text-center text-sm">
                        {t('dashboard.no_data')}
                    </p>
                </CardContent>
            </Card>
        );
    }

    const visible = showAll ? data : data.slice(0, MAX_VISIBLE);
    const maxValue = Math.max(...data.map((c) => Math.max(c.total, c.previous_total, c.budget ?? 0)));

    return (
        <Card>
            <CardHeader>
                <CardTitle>{t('dashboard.by_category')}</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
                {visible.map((cat) => {
                    const budgetAmount = cat.budget ?? 0;
                    const hasBudget = budgetAmount > 0;
                    const budgetPercent = hasBudget ? (cat.total / budgetAmount) * 100 : 0;

                    const barPercent = hasBudget
                        ? Math.min(budgetPercent, 100)
                        : maxValue > 0 ? (cat.total / maxValue) * 100 : 0;

                    const statusColor = hasBudget
                        ? budgetPercent < 75
                            ? 'bg-emerald-500'
                            : budgetPercent < 100
                                ? 'bg-amber-500'
                                : 'bg-red-500'
                        : cat.previous_total === 0
                            ? 'bg-primary'
                            : (cat.total - cat.previous_total) <= 0
                                ? 'bg-emerald-500'
                                : (cat.total - cat.previous_total) / cat.previous_total > 0.2
                                    ? 'bg-red-500'
                                    : 'bg-amber-500';

                    return (
                        <div key={cat.name} className="space-y-1">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <span className="text-base">{cat.icon}</span>
                                    <span className="text-sm font-medium">
                                        {t('categories.' + cat.name)}
                                    </span>
                                </div>
                                <div className="flex items-baseline gap-1.5 text-right">
                                    <span className="text-sm font-semibold tabular-nums">
                                        {formatMoney(cat.total)}
                                    </span>
                                    {hasBudget ? (
                                        <span className="text-muted-foreground text-xs tabular-nums">
                                            / {formatMoney(budgetAmount)}
                                        </span>
                                    ) : cat.previous_total > 0 ? (
                                        <span className="text-muted-foreground text-xs tabular-nums">
                                            {t('dashboard.vs_amount_last_month', {
                                                amount: formatMoney(cat.previous_total),
                                            })}
                                        </span>
                                    ) : null}
                                </div>
                            </div>
                            <div className="bg-muted h-1.5 w-full overflow-hidden rounded-full">
                                <div
                                    className={`h-full rounded-full transition-all ${statusColor}`}
                                    style={{ width: `${barPercent}%` }}
                                />
                            </div>
                            {hasBudget && budgetPercent >= 100 && (
                                <p className="text-[11px] text-red-500">
                                    {t('budget.category_over', {
                                        amount: formatMoney(cat.total - budgetAmount),
                                    })}
                                </p>
                            )}
                        </div>
                    );
                })}
                {data.length > MAX_VISIBLE && (
                    <Button
                        variant="ghost"
                        size="sm"
                        className="w-full gap-1"
                        onClick={() => setShowAll(!showAll)}
                    >
                        {showAll ? (
                            <>
                                {t('dashboard.show_fewer')}
                                <ChevronUp className="h-3.5 w-3.5" />
                            </>
                        ) : (
                            <>
                                {t('dashboard.show_all_categories', { count: data.length })}
                                <ChevronDown className="h-3.5 w-3.5" />
                            </>
                        )}
                    </Button>
                )}
            </CardContent>
        </Card>
    );
}
