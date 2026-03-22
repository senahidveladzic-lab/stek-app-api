<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $householdId = $user->household_id;
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d');

        $monthExpenses = Expense::query()
            ->forHousehold($householdId)
            ->inDateRange($startOfMonth, $endOfMonth);

        $totalThisMonth = (float) $monthExpenses->sum('amount');
        $transactionCount = $monthExpenses->count();
        $daysInMonth = $now->day;
        $dailyAverage = $daysInMonth > 0 ? round($totalThisMonth / $daysInMonth, 2) : 0;

        $previousStart = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $previousEnd = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
        $previousMonthTotal = (float) Expense::query()
            ->forHousehold($householdId)
            ->inDateRange($previousStart, $previousEnd)
            ->sum('amount');

        $previousSamePeriodEnd = $now->copy()->subMonth()->format('Y-m-d');
        $previousMonthSamePeriodTotal = (float) Expense::query()
            ->forHousehold($householdId)
            ->inDateRange($previousStart, $previousSamePeriodEnd)
            ->sum('amount');

        $previousCategoryTotals = Expense::query()
            ->forHousehold($householdId)
            ->inDateRange($previousStart, $previousEnd)
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->pluck('total', 'name');

        $byCategory = Expense::query()
            ->forHousehold($householdId)
            ->inDateRange($startOfMonth, $endOfMonth)
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.icon', 'categories.color', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('categories.id', 'categories.name', 'categories.icon', 'categories.color')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($cat) => [
                'name' => $cat->name,
                'icon' => $cat->icon,
                'color' => $cat->color,
                'total' => (float) $cat->total,
                'previous_total' => (float) ($previousCategoryTotals[$cat->name] ?? 0),
            ]);

        $memberSpending = Expense::query()
            ->where('expenses.household_id', $householdId)
            ->inDateRange($startOfMonth, $endOfMonth)
            ->join('users', 'expenses.user_id', '=', 'users.id')
            ->select('users.id as user_id', 'users.name as user_name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->get();

        $recentExpenses = Expense::query()
            ->forHousehold($householdId)
            ->with(['category', 'user:id,name'])
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $monthBudgets = Budget::query()
            ->forHousehold($householdId)
            ->forMonth($startOfMonth)
            ->with('category')
            ->get();

        $overallBudget = $monthBudgets->firstWhere('category_id', null);
        $categoryBudgetMap = $monthBudgets
            ->whereNotNull('category_id')
            ->keyBy(fn (Budget $b) => $b->category->name);

        $byCategory = $byCategory->map(fn ($cat) => array_merge($cat, [
            'budget' => isset($categoryBudgetMap[$cat['name']])
                ? (float) $categoryBudgetMap[$cat['name']]->amount
                : null,
        ]));

        return Inertia::render('dashboard', [
            'summary' => [
                'total_this_month' => $totalThisMonth,
                'transaction_count' => $transactionCount,
                'daily_average' => $dailyAverage,
                'previous_month_total' => $previousMonthTotal,
                'previous_month_same_period_total' => $previousMonthSamePeriodTotal,
                'currency' => $user->household->default_currency ?? $user->default_currency,
                'by_category' => $byCategory,
                'member_spending' => $memberSpending,
                'recent_expenses' => $recentExpenses,
                'budget' => $overallBudget ? (float) $overallBudget->amount : null,
            ],
        ]);
    }
}
