<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\AiUsageLimitExceededException;
use App\Exceptions\ExpenseParseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoiceExpenseRequest;
use App\Services\ExpenseAIService;
use App\Services\HouseholdAiUsageService;
use App\Services\WhisperService;
use Illuminate\Http\JsonResponse;

class ExpenseVoiceController extends Controller
{
    public function store(
        VoiceExpenseRequest $request,
        ExpenseAIService $aiService,
        WhisperService $whisperService,
        HouseholdAiUsageService $householdAiUsageService,
    ): JsonResponse {
        try {
            $householdAiUsageService->consume($request->user());

            $text = $request->hasFile('audio')
                ? $whisperService->transcribe($request->file('audio'), app()->getLocale())
                : $request->validated('text');

            $user = $request->user();
            $household = $user->household;
            $tags = $household?->tags()
                ->orderBy('name')
                ->get(['id', 'name', 'color'])
                ->map(fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ])
                ->values()
                ->all() ?? [];

            $parsed = $aiService->parse(
                $text,
                app()->getLocale(),
                $household->default_currency ?? $user->default_currency,
                $tags,
            );

            $parsed['description'] = $text;

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
