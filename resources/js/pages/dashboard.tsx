import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from '@/hooks/use-translation';
import { useBreadcrumbs } from '@/hooks/use-breadcrumbs';
import appLayout from '@/layouts/app-layout';
import type { Summary } from './dashboard/types';
import { HeroStat } from './dashboard/hero-stat';
import { HouseholdBalance } from './dashboard/household-balance';
import { CategorySpending } from './dashboard/category-spending';
import { MonthlyComparison } from './dashboard/monthly-comparison';
import { RecentExpenses } from './dashboard/recent-expenses';
import { EmptyState } from './dashboard/empty-state';

export default function Dashboard() {
    const { t } = useTranslation();
    const { summary } = usePage<{ summary: Summary }>().props;

    useBreadcrumbs([{ title: t('nav.dashboard'), href: '/dashboard' }]);

    const hasData = summary.transaction_count > 0;

    return (
        <>
            <Head title={t('dashboard.title')} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {hasData ? (
                    <>
                        <div className={`grid gap-6 ${summary.previous_month_total > 0 ? 'lg:grid-cols-3' : 'lg:grid-cols-2'}`}>
                            <HeroStat total={summary.total_this_month} previousTotal={summary.previous_month_total} budget={summary.budget} />
                            <HouseholdBalance
                                members={summary.member_spending}
                                transactionCount={summary.transaction_count}
                                dailyAverage={summary.daily_average}
                            />
                            {summary.previous_month_total > 0 && (
                                <MonthlyComparison
                                    totalThisMonth={summary.total_this_month}
                                    previousMonthSamePeriodTotal={summary.previous_month_same_period_total}
                                />
                            )}
                        </div>
                        <CategorySpending data={summary.by_category} />
                        <RecentExpenses expenses={summary.recent_expenses} />
                    </>
                ) : (
                    <EmptyState />
                )}
            </div>
        </>
    );
}

Dashboard.layout = appLayout;
