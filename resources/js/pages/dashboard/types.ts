export type CategoryData = {
    name: string;
    icon: string;
    color: string;
    total: number;
    previous_total: number;
    budget: number | null;
};

export type MemberSpending = {
    user_id: number;
    user_name: string;
    total: number;
};

export type ExpenseData = {
    id: number;
    amount: string;
    currency: string;
    original_amount: string | null;
    original_currency: string | null;
    description: string | null;
    expense_date: string;
    category: { name: string; icon: string; color: string } | null;
    user: { id: number; name: string } | null;
};

export type Summary = {
    total_this_month: number;
    transaction_count: number;
    daily_average: number;
    previous_month_total: number;
    previous_month_same_period_total: number;
    currency: string;
    by_category: CategoryData[];
    member_spending: MemberSpending[];
    recent_expenses: ExpenseData[];
    budget: number | null;
};
