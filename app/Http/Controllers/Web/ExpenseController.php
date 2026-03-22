<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ExpenseParseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Requests\VoiceExpenseRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Services\CurrencyConversionService;
use App\Services\ExpenseAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $householdId = $user->household_id;

        $from = $request->string('from')->value() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->value() ?: now()->endOfMonth()->toDateString();

        $expenses = Expense::query()
            ->forHousehold($householdId)
            ->with(['category', 'user:id,name'])
            ->when($request->filled('category_id'), fn ($q) => $q->inCategory($request->integer('category_id')))
            ->inDateRange($from, $to)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($q) use ($search) {
                    $q->where('merchant', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $monthTotal = (float) Expense::query()
            ->forHousehold($householdId)
            ->inDateRange($from, $to)
            ->sum('amount');

        $yearStart = now()->startOfYear()->toDateString();
        $yearEnd = now()->endOfYear()->toDateString();
        $yearTotal = (float) Expense::query()
            ->forHousehold($householdId)
            ->inDateRange($yearStart, $yearEnd)
            ->sum('amount');

        return Inertia::render('expenses/index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'filters' => [
                'category_id' => $request->input('category_id'),
                'from' => $from,
                'to' => $to,
                'search' => $request->input('search'),
            ],
            'month_total' => $monthTotal,
            'year_total' => $yearTotal,
        ]);
    }

    public function store(StoreExpenseRequest $request, CurrencyConversionService $conversionService): RedirectResponse
    {
        $user = $request->user();
        $household = $user->household;
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

        $user->expenses()->create($data);

        return back()->with('success', __('common.success'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense, CurrencyConversionService $conversionService): RedirectResponse
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

        return back()->with('success', __('common.success'));
    }

    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        $expense->delete();

        return back()->with('success', __('common.success'));
    }

    public function voice(VoiceExpenseRequest $request, ExpenseAIService $aiService): JsonResponse
    {
        try {
            $parsed = $aiService->parse(
                $request->validated('text'),
                app()->getLocale(),
                $request->user()->household->default_currency ?? $request->user()->default_currency,
            );

            $category = Category::query()->where('name', $parsed['category_key'])->first();

            return response()->json([
                'data' => [
                    ...$parsed,
                    'category_id' => $category?->id,
                ],
            ]);
        } catch (ExpenseParseException) {
            return response()->json([
                'message' => __('errors.ai_parse_failed'),
            ], 422);
        }
    }
}
