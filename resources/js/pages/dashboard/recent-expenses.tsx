import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslation } from '@/hooks/use-translation';
import type { ExpenseData } from './types';

type RecentExpensesProps = {
    expenses: ExpenseData[];
};

function groupByDate(expenses: ExpenseData[]): Record<string, ExpenseData[]> {
    const groups: Record<string, ExpenseData[]> = {};
    for (const expense of expenses) {
        const date = expense.expense_date;
        if (!groups[date]) {
            groups[date] = [];
        }
        groups[date].push(expense);
    }
    return groups;
}

export function RecentExpenses({ expenses }: RecentExpensesProps) {
    const { t, formatMoney, formatDate } = useTranslation();

    if (expenses.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>{t('dashboard.recent_expenses')}</CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-muted-foreground py-8 text-center text-sm">
                        {t('dashboard.no_data')}
                    </p>
                </CardContent>
            </Card>
        );
    }

    const grouped = groupByDate(expenses);

    return (
        <Card>
            <CardHeader className="flex-row items-center justify-between">
                <CardTitle>{t('dashboard.recent_expenses')}</CardTitle>
                <Button variant="ghost" size="sm" asChild>
                    <Link href="/expenses" className="text-primary gap-1">
                        {t('dashboard.view_all_expenses')}
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                </Button>
            </CardHeader>
            <CardContent>
                <div className="space-y-4">
                    {Object.entries(grouped).map(([date, items]) => {
                        const dayTotal = items.reduce((sum, e) => sum + parseFloat(e.amount), 0);
                        return (
                            <div key={date}>
                                <div className="bg-muted/50 -mx-1 mb-1 flex items-center justify-between rounded-md px-3 py-1.5">
                                    <span className="text-muted-foreground text-xs font-medium">
                                        {formatDate(date)}
                                    </span>
                                    <span className="text-muted-foreground text-xs font-semibold tabular-nums">
                                        {formatMoney(dayTotal)}
                                    </span>
                                </div>
                                <div className="divide-border divide-y">
                                    {items.map((expense) => (
                                        <div
                                            key={expense.id}
                                            className="hover:bg-muted/30 flex items-center justify-between rounded-lg px-2 py-2.5 transition-colors"
                                        >
                                            <div className="flex items-center gap-3">
                                                <div
                                                    className="flex h-9 w-9 items-center justify-center rounded-lg text-lg"
                                                    style={{
                                                        backgroundColor: expense.category?.color
                                                            ? `${expense.category.color}18`
                                                            : undefined,
                                                    }}
                                                >
                                                    {expense.category?.icon ?? '📦'}
                                                </div>
                                                <div className="min-w-0">
                                                    <p className="truncate text-sm font-medium">
                                                        {expense.merchant ??
                                                            expense.description ??
                                                            (expense.category
                                                                ? t('categories.' + expense.category.name)
                                                                : '')}
                                                    </p>
                                                    <div className="flex items-center gap-1.5">
                                                        {expense.category && (
                                                            <span className="text-muted-foreground text-[11px]">
                                                                {t('categories.' + expense.category.name)}
                                                            </span>
                                                        )}
                                                        {expense.user && (
                                                            <>
                                                                <span className="text-muted-foreground/50 text-[11px]">·</span>
                                                                <span className="text-muted-foreground text-[11px]">
                                                                    {expense.user.name}
                                                                </span>
                                                            </>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="shrink-0 pl-4 text-right">
                                                <span className="text-sm font-semibold tabular-nums">
                                                    {formatMoney(parseFloat(expense.amount))}
                                                </span>
                                                {expense.original_amount && expense.original_currency && expense.original_currency !== expense.currency && (
                                                    <div>
                                                        <Badge variant="outline" className="text-[10px] px-1.5 py-0">
                                                            {parseFloat(expense.original_amount).toFixed(2)} {expense.original_currency}
                                                        </Badge>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </CardContent>
        </Card>
    );
}
