<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\AiUsageLimitExceededException;
use App\Exceptions\ExpenseParseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoiceExpenseRequest;
use App\Services\ExpenseAIService;
use App\Services\HouseholdAiUsageService;
use Illuminate\Http\JsonResponse;

class ExpenseVoiceController extends Controller
{
    public function store(
        VoiceExpenseRequest $request,
        ExpenseAIService $aiService,
        HouseholdAiUsageService $householdAiUsageService
    ): JsonResponse {
        try {
            $householdAiUsageService->consume($request->user());

            $parsed = $aiService->parse(
                $request->validated('text'),
                app()->getLocale(),
                $request->user()->default_currency,
            );

            return response()->json(['data' => $parsed]);
        } catch (ExpenseParseException) {
            return response()->json([
                'message' => __('errors.ai_parse_failed'),
            ], 422);
        } catch (AiUsageLimitExceededException) {
            return response()->json([
                'message' => __('errors.ai_usage_limit_reached'),
            ], 429);
        }
    }
}
