import { Hash, TrendingUp } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { useTranslation } from '@/hooks/use-translation';
import type { MemberSpending } from './types';

type HouseholdBalanceProps = {
    members: MemberSpending[];
    transactionCount: number;
    dailyAverage: number;
};

const MEMBER_COLORS = [
    'bg-primary',
    'bg-chart-2',
    'bg-chart-3',
    'bg-chart-4',
];

export function HouseholdBalance({ members, transactionCount, dailyAverage }: HouseholdBalanceProps) {
    const { t, formatMoney } = useTranslation();

    if (members.length <= 1) {
        return (
            <div className="from-primary/8 via-primary/3 to-card relative overflow-hidden rounded-xl border bg-gradient-to-br p-5 md:p-6">
                <p className="text-muted-foreground mb-4 text-xs font-medium uppercase tracking-wide">
                    {t('dashboard.transaction_overview')}
                </p>
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <div className="bg-primary/10 mb-2 flex h-10 w-10 items-center justify-center rounded-lg">
                            <Hash className="text-primary h-5 w-5" />
                        </div>
                        <p className="text-3xl font-bold tracking-tight tabular-nums">{transactionCount}</p>
                        <p className="text-muted-foreground mt-0.5 text-xs">{t('dashboard.transaction_count')}</p>
                    </div>
                    <div>
                        <div className="bg-primary/10 mb-2 flex h-10 w-10 items-center justify-center rounded-lg">
                            <TrendingUp className="text-primary h-5 w-5" />
                        </div>
                        <p className="text-3xl font-bold tracking-tight tabular-nums">{formatMoney(dailyAverage)}</p>
                        <p className="text-muted-foreground mt-0.5 text-xs">{t('dashboard.daily_average')}</p>
                    </div>
                </div>
            </div>
        );
    }

    const sorted = [...members].sort((a, b) => b.total - a.total);
    const top = sorted[0];
    const second = sorted[1];
    const diff = top.total - second.total;
    const grandTotal = sorted.reduce((sum, m) => sum + Number(m.total), 0);

    return (
        <Card>
            <CardContent className="space-y-3 py-4">
                <p className="text-sm font-medium">
                    {t('dashboard.member_paid_more', {
                        name: top.user_name.split(' ')[0],
                        amount: formatMoney(diff),
                        other: second.user_name.split(' ')[0],
                    })}
                </p>
                <div className="flex h-3 w-full gap-0.5 overflow-hidden rounded-full">
                    {sorted.map((member, i) => {
                        const percent = grandTotal > 0 ? (Number(member.total) / grandTotal) * 100 : 0;
                        return (
                            <div
                                key={member.user_id}
                                className={`${MEMBER_COLORS[i % MEMBER_COLORS.length]} first:rounded-l-full last:rounded-r-full`}
                                style={{ width: `${percent}%` }}
                                title={`${member.user_name}: ${formatMoney(Number(member.total))}`}
                            />
                        );
                    })}
                </div>
                <div className="flex justify-between text-xs">
                    {sorted.map((member, i) => (
                        <div key={member.user_id} className="flex items-center gap-1.5">
                            <div className={`h-2 w-2 rounded-full ${MEMBER_COLORS[i % MEMBER_COLORS.length]}`} />
                            <span className="text-muted-foreground">
                                {member.user_name.split(' ')[0]}: {formatMoney(Number(member.total))}
                            </span>
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}
