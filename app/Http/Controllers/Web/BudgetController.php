<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BudgetController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $householdId = $user->household_id;

        $month = $request->input('month', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $monthDate = Carbon::parse($month)->startOfMonth()->format('Y-m-d');

        $budgets = Budget::query()
            ->forHousehold($householdId)
            ->forMonth($monthDate)
            ->with('category')
            ->get();

        if ($budgets->isEmpty()) {
            $budgets = $this->copyFromPreviousMonth($householdId, $monthDate);
        }

        $overallBudget = $budgets->firstWhere('category_id', null);
        $categoryBudgets = $budgets->whereNotNull('category_id')->values();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'icon', 'color']);

        return Inertia::render('budgets/index', [
            'month' => $monthDate,
            'overall_budget' => $overallBudget ? (float) $overallBudget->amount : null,
            'category_budgets' => $categoryBudgets->map(fn (Budget $b) => [
                'id' => $b->id,
                'category_id' => $b->category_id,
                'category_name' => $b->category->name,
                'category_icon' => $b->category->icon,
                'category_color' => $b->category->color,
                'amount' => (float) $b->amount,
            ]),
            'categories' => $categories,
            'currency' => $user->household->default_currency ?? $user->default_currency,
        ]);
    }

    public function store(SaveBudgetRequest $request): RedirectResponse
    {
        $user = $request->user();
        $householdId = $user->household_id;
        $monthDate = Carbon::parse($request->validated('month'))->startOfMonth()->format('Y-m-d');

        if ($request->filled('overall_amount')) {
            $this->upsertBudget($householdId, null, $monthDate, $request->validated('overall_amount'));
        } else {
            Budget::query()
                ->forHousehold($householdId)
                ->forMonth($monthDate)
                ->overall()
                ->delete();
        }

        $existingCategoryIds = [];

        if ($request->has('categories')) {
            foreach ($request->validated('categories') as $cat) {
                $this->upsertBudget($householdId, $cat['category_id'], $monthDate, $cat['amount']);
                $existingCategoryIds[] = $cat['category_id'];
            }
        }

        Budget::query()
            ->forHousehold($householdId)
            ->forMonth($monthDate)
            ->byCategory()
            ->whereNotIn('category_id', $existingCategoryIds)
            ->delete();

        return redirect()->route('budgets.index', ['month' => $monthDate]);
    }

    private function upsertBudget(int $householdId, ?int $categoryId, string $month, float $amount): void
    {
        $query = Budget::query()
            ->forHousehold($householdId)
            ->forMonth($month);

        if ($categoryId === null) {
            $query->overall();
        } else {
            $query->where('category_id', $categoryId);
        }

        $budget = $query->first();

        if ($budget) {
            $budget->update(['amount' => $amount]);
        } else {
            Budget::query()->create([
                'household_id' => $householdId,
                'category_id' => $categoryId,
                'month' => $month,
                'amount' => $amount,
            ]);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Budget>
     */
    private function copyFromPreviousMonth(int $householdId, string $currentMonth): \Illuminate\Database\Eloquent\Collection
    {
        $previousMonth = Carbon::parse($currentMonth)->subMonth()->startOfMonth()->format('Y-m-d');

        $previousBudgets = Budget::query()
            ->forHousehold($householdId)
            ->forMonth($previousMonth)
            ->get();

        if ($previousBudgets->isEmpty()) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        $copied = [];
        foreach ($previousBudgets as $budget) {
            $copied[] = Budget::query()->create([
                'household_id' => $householdId,
                'category_id' => $budget->category_id,
                'month' => $currentMonth,
                'amount' => $budget->amount,
            ]);
        }

        return new \Illuminate\Database\Eloquent\Collection($copied);
    }
}
