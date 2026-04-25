<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Services\CurrencyConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $expenses = Expense::query()
            ->forHousehold($request->user()->household_id)
            ->with(['category', 'user:id,name'])
            ->when($request->filled('category_id'), fn ($q) => $q->inCategory($request->integer('category_id')))
            ->when(
                $request->filled('from') && $request->filled('to'),
                fn ($q) => $q->inDateRange($request->string('from'), $request->string('to')),
            )
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(20);

        return ExpenseResource::collection($expenses);
    }

    public function store(StoreExpenseRequest $request, CurrencyConversionService $conversionService): JsonResponse
    {
        $user = $request->user();
        $household = $user->household;

        if (! $household) {
            return response()->json(['message' => 'User is not assigned to a household.'], 422);
        }

        $validated = $request->validated();
        $expenseCurrency = $validated['currency'] ?? $household->default_currency;

        $data = [
            ...$validated,
            'currency' => $household->default_currency,
            'household_id' => $household->id,
        ];

        if ($expenseCurrency !== $household->default_currency) {
            $result = $conversionService->convert(
                (float) $validated['amount'],
                $expenseCurrency,
                $household->default_currency,
            );
            $data['amount'] = $result['converted'];
            $data['original_amount'] = $validated['amount'];
            $data['original_currency'] = $expenseCurrency;
        }

        $expense = $user->expenses()->create($data);
        $expense->load('category');

        return (new ExpenseResource($expense))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense, CurrencyConversionService $conversionService): ExpenseResource
    {
        Gate::authorize('update', $expense);

        $validated = $request->validated();
        $household = $request->user()->household;

        if (isset($validated['amount']) || isset($validated['currency'])) {
            $expenseCurrency = $validated['currency'] ?? $expense->original_currency ?? $household->default_currency;
            $amount = $validated['amount'] ?? $expense->original_amount ?? $expense->amount;

            if ($expenseCurrency !== $household->default_currency) {
                $result = $conversionService->convert(
                    (float) $amount,
                    $expenseCurrency,
                    $household->default_currency,
                );
                $validated['amount'] = $result['converted'];
                $validated['original_amount'] = $amount;
                $validated['original_currency'] = $expenseCurrency;
                $validated['currency'] = $household->default_currency;
            } else {
                $validated['original_amount'] = null;
                $validated['original_currency'] = null;
                $validated['currency'] = $household->default_currency;
            }
        }

        $expense->update($validated);
        $expense->load('category');

        return new ExpenseResource($expense);
    }

    public function destroy(Request $request, Expense $expense): JsonResponse
    {
        Gate::authorize('delete', $expense);

        $expense->delete();

        return response()->json(null, 204);
    }
}
