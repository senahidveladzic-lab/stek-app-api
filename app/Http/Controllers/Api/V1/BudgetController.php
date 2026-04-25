<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveBudgetRequest;
use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request): JsonResponse
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

        return response()->json([
            'data' => $this->budgetPayload($user, $budgets, $monthDate),
        ]);
    }

    public function store(SaveBudgetRequest $request): JsonResponse
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

        $budgets = Budget::query()
            ->forHousehold($householdId)
            ->forMonth($monthDate)
            ->with('category')
            ->get();

        return response()->json([
            'message' => 'Budget saved successfully.',
            'data' => $this->budgetPayload($user, $budgets, $monthDate),
        ]);
    }

    /**
     * @param  Collection<int, Budget>  $budgets
     * @return array{
     *     month: string,
     *     overall_budget: float|null,
     *     category_budgets: \Illuminate\Support\Collection<int, array{
     *         id: int,
     *         category_id: int|null,
     *         category_name: string,
     *         category_icon: string,
     *         category_color: string,
     *         amount: float
     *     }>,
     *     currency: string
     * }
     */
    private function budgetPayload(User $user, Collection $budgets, string $monthDate): array
    {
        $overallBudget = $budgets->firstWhere('category_id', null);
        $categoryBudgets = $budgets->whereNotNull('category_id')->values();

        return [
            'month' => $monthDate,
            'overall_budget' => $overallBudget ? (float) $overallBudget->amount : null,
            'category_budgets' => $categoryBudgets->map(fn (Budget $budget) => [
                'id' => $budget->id,
                'category_id' => $budget->category_id,
                'category_name' => $budget->category->name,
                'category_icon' => $budget->category->icon,
                'category_color' => $budget->category->color,
                'amount' => (float) $budget->amount,
            ]),
            'currency' => $user->household->default_currency ?? $user->default_currency,
        ];
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
    private function copyFromPreviousMonth(int $householdId, string $currentMonth): Collection
    {
        $previousMonth = Carbon::parse($currentMonth)->subMonth()->startOfMonth()->format('Y-m-d');

        $previousBudgets = Budget::query()
            ->forHousehold($householdId)
            ->forMonth($previousMonth)
            ->get();

        if ($previousBudgets->isEmpty()) {
            return new Collection;
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

        return new Collection($copied);
    }
}
