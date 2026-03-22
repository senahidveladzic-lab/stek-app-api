# Dashboard Redesign: Replace Charts with Actionable Data

## Context

This is a household expense tracker (Laravel + Inertia + React + Tailwind v4). The current dashboard at `resources/js/pages/dashboard.tsx` has generic CMS-style charts (category bar chart, daily spending area chart, 6-month trend area chart) that aren't useful. Based on research of Copilot Money, YNAB, Spendee, Splitwise, Monefy, Bluecoins, and Monarch Money — **the most loved finance apps avoid traditional charts entirely** and instead surface actionable, scannable information.

The app recently added household multi-tenancy (multiple family members share expenses). The backend controller is `app/Http/Controllers/Web/DashboardController.php`.

## What to Build

Replace the current chart-heavy dashboard with this layout (top to bottom):

### 1. Hero: Keep but enhance
**File:** `resources/js/pages/dashboard/hero-stat.tsx`

Keep the current hero stat showing "total this month" with the % vs last month badge. It's good. But add a **burn rate indicator** below it: a thin progress bar showing two things overlaid — (1) how far through the month we are (time-based, e.g., 8/31 = 26%) and (2) what % of last month's total we've already spent. If spending % > time %, the bar should tint red/orange as a subtle warning. If under, green.

Example: On March 8th, if we've spent 1,200 KM and last month total was 3,000 KM, that's 40% spent but only 26% of the month gone — bar shows yellow/orange.

### 2. NEW: Household Balance Card (replace StatCards)
**Replace:** `resources/js/pages/dashboard/stat-cards.tsx`
**New component:** `resources/js/pages/dashboard/household-balance.tsx`

For multi-member households, show a card with: "**[Member A]** has paid **X KM** more than **[Member B]** this month" with a simple horizontal split bar showing each member's contribution proportion. Use member initials as avatars.

For single-member households (just the user), show daily average and transaction count as compact inline stats instead (no big cards needed — just a single line like "23 transactions · 47 KM/day average").

**Backend change needed:** Add `member_spending` to the dashboard summary response — an array of `{ user_id, user_name, total }` for each household member this month. Add this to `DashboardController@index`.

### 3. NEW: Category Budget Bars (replace CategoryChart)
**Replace:** `resources/js/pages/dashboard/category-chart.tsx` (the recharts bar chart)
**New component:** `resources/js/pages/dashboard/category-spending.tsx`

A vertical list of spending categories. Each row has:
- Left: category icon + translated name
- Center: horizontal progress bar (filled portion = this month's spend, total width = last month's spend in that category as the reference). Color: green if under last month's pace, yellow if close, red/orange if over.
- Right: amount spent + "vs X KM last month" in small muted text

Sort by highest spend first. Max 6-8 categories visible, rest collapsed behind a "show all" toggle.

This replaces the useless bar chart with something instantly scannable. No recharts dependency needed for this — pure Tailwind progress bars.

**Backend change needed:** The existing `by_category` data includes `name, icon, color, total`. Add `previous_total` to each category (same category's total from last month). This gives context without needing explicit budgets.

### 4. NEW: Monthly Comparison (replace MonthlyTrend)
**Replace:** `resources/js/pages/dashboard/monthly-trend.tsx` (the 6-month area chart)
**New component:** `resources/js/pages/dashboard/monthly-comparison.tsx`

A single compact card with a text-based comparison:

> "Potrošili ste **1.240 KM** ovaj mjesec. To je **180 KM više** nego prošli mjesec u ovom periodu."
>
> or in English: "You've spent **1,240 KM** this month. That's **180 KM more** than last month at this point."

Use a green down-arrow if under, red up-arrow if over. The "at this point" comparison means: compare spending from day 1 to day N of this month vs day 1 to day N of last month (same number of days). This is far more meaningful than comparing a partial month to a complete month.

**Backend change needed:** Add `previous_month_same_period_total` to the summary — last month's expenses from day 1 to the same day-of-month as today.

### 5. Keep: Recent Expenses (enhanced)
**File:** `resources/js/pages/dashboard/recent-expenses.tsx`

Keep but enhance:
- Group expenses by day with a **running daily total** as the section header (e.g., "Utorak, 8. mart — 67,20 KM")
- Show **who added** each expense (small text with member name, since it's now household-scoped)
- Show the **original currency pill** if `original_amount` exists (same pattern as the expenses table)
- Increase from 5 to 10 recent expenses

**Backend change needed:** Already returning `user:id,name` from the controller. Bump limit from 5 to 10.

### 6. DELETE: SpendingTimeline
**Remove:** `resources/js/pages/dashboard/spending-timeline.tsx`

The daily spending area chart adds no value. The recent expenses list grouped by day already shows daily spending patterns. Delete this component and its recharts dependency.

### 7. DELETE: ChartTooltip
**Remove:** `resources/js/pages/dashboard/chart-tooltip.tsx`

No more recharts, no need for chart tooltips.

---

## Backend Changes Summary

**`app/Http/Controllers/Web/DashboardController.php`** needs these additions to the summary response:

```php
// 1. Member spending breakdown (for household balance card)
'member_spending' => Expense::query()
    ->forHousehold($householdId)
    ->inDateRange($startOfMonth, $endOfMonth)
    ->join('users', 'expenses.user_id', '=', 'users.id')
    ->select('users.id', 'users.name', DB::raw('SUM(expenses.amount) as total'))
    ->groupBy('users.id', 'users.name')
    ->get(),

// 2. Previous month category totals (add previous_total to each by_category item)
// Query last month's by_category with the same grouping, then merge

// 3. Same-period comparison
'previous_month_same_period_total' => (float) Expense::query()
    ->forHousehold($householdId)
    ->inDateRange($previousStart, $now->copy()->subMonth()->format('Y-m-d'))
    ->sum('amount'),
```

Also update the API dashboard controller (`app/Http/Controllers/Api/V1/DashboardController.php`) to match.

Bump recent expenses limit from 5 to 10 and ensure `user:id,name` is loaded (already done).

---

## Translation Keys to Add

**`lang/bs.json`:**
```json
"dashboard.burn_rate": "Tempo potrošnje",
"dashboard.household_balance": "Balans domaćinstva",
"dashboard.paid_more": ":name je platio/la :amount više ovaj mjesec",
"dashboard.spending_by_category": "Potrošnja po kategoriji",
"dashboard.vs_last_month_category": "od :amount prošli mjesec",
"dashboard.show_all_categories": "Prikaži sve kategorije",
"dashboard.monthly_comparison": "U poredjenju sa prošlim mjesecom",
"dashboard.more_than_last_month": ":amount više nego prošli mjesec u ovom periodu",
"dashboard.less_than_last_month": ":amount manje nego prošli mjesec u ovom periodu",
"dashboard.same_as_last_month": "Isto kao prošli mjesec u ovom periodu"
```

**`lang/en.json`:**
```json
"dashboard.burn_rate": "Spending pace",
"dashboard.household_balance": "Household balance",
"dashboard.paid_more": ":name has paid :amount more this month",
"dashboard.spending_by_category": "Spending by category",
"dashboard.vs_last_month_category": "of :amount last month",
"dashboard.show_all_categories": "Show all categories",
"dashboard.monthly_comparison": "Compared to last month",
"dashboard.more_than_last_month": ":amount more than last month at this point",
"dashboard.less_than_last_month": ":amount less than last month at this point",
"dashboard.same_as_last_month": "Same as last month at this point"
```

---

## Files to Change

| Action | File |
|--------|------|
| Edit | `app/Http/Controllers/Web/DashboardController.php` |
| Edit | `app/Http/Controllers/Api/V1/DashboardController.php` |
| Edit | `resources/js/pages/dashboard.tsx` |
| Edit | `resources/js/pages/dashboard/types.ts` |
| Edit | `resources/js/pages/dashboard/hero-stat.tsx` (add burn rate bar) |
| Edit | `resources/js/pages/dashboard/recent-expenses.tsx` (group by day, add user, currency pill) |
| Create | `resources/js/pages/dashboard/household-balance.tsx` |
| Create | `resources/js/pages/dashboard/category-spending.tsx` |
| Create | `resources/js/pages/dashboard/monthly-comparison.tsx` |
| Delete | `resources/js/pages/dashboard/category-chart.tsx` |
| Delete | `resources/js/pages/dashboard/spending-timeline.tsx` |
| Delete | `resources/js/pages/dashboard/monthly-trend.tsx` |
| Delete | `resources/js/pages/dashboard/chart-tooltip.tsx` |
| Edit | `lang/bs.json` (add new keys) |
| Edit | `lang/en.json` (add new keys) |
| Edit | `tests/Feature/DashboardTest.php` (update assertions) |

## Design Guidelines
- No recharts. Pure Tailwind for all visualizations (progress bars, split bars).
- Use existing shadcn/ui components: Card, Badge, Button.
- Keep the `useTranslation` hook for all text + `formatMoney` for amounts.
- Color scheme: green = good/under, yellow = caution, red/destructive = over budget. Use Tailwind's `text-green-600`, `text-amber-500`, `text-red-500` (with dark mode variants).
- Mobile-first. Everything should stack cleanly on small screens.
- Keep it scannable. A user should understand their financial state in under 3 seconds.

## Verification
1. `php artisan test --compact` — all tests pass
2. `vendor/bin/pint --dirty --format agent` — code style
3. `pnpm run build` — frontend compiles
4. Visual check: dashboard shows actionable data, no orphaned chart imports
