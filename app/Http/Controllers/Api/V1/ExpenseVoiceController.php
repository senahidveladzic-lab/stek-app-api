<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ExpenseParseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoiceExpenseRequest;
use App\Services\ExpenseAIService;
use Illuminate\Http\JsonResponse;

class ExpenseVoiceController extends Controller
{
    public function store(VoiceExpenseRequest $request, ExpenseAIService $aiService): JsonResponse
    {
        try {
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
        }
    }
}
