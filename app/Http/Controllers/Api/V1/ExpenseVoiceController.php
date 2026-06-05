<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\AiUsageLimitExceededException;
use App\Exceptions\DailyVoiceLimitExceededException;
use App\Exceptions\ExpenseParseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoiceExpenseRequest;
use App\Services\ExpenseAIService;
use App\Services\HouseholdAiUsageService;
use App\Services\MobileVoiceUsageService;
use App\Services\VoiceCorrectionService;
use App\Services\WhisperService;
use Illuminate\Http\JsonResponse;

class ExpenseVoiceController extends Controller
{
    public function store(
        VoiceExpenseRequest $request,
        ExpenseAIService $aiService,
        WhisperService $whisperService,
        HouseholdAiUsageService $householdAiUsageService,
        MobileVoiceUsageService $mobileVoiceUsageService,
        VoiceCorrectionService $correctionService,
    ): JsonResponse {
        try {
            $user = $request->user();

            if ($user->hasBillingAccess()) {
                $householdAiUsageService->consume($user);
            } else {
                $mobileVoiceUsageService->consume($user);
            }

            $text = $request->hasFile('audio')
                ? $whisperService->transcribe($request->file('audio'), app()->getLocale())
                : $request->validated('text');

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

            $userCorrections = $correctionService->userExamples($user->id);
            $globalCorrections = $correctionService->globalExamples();

            $parsed = $aiService->parse(
                $text,
                app()->getLocale(),
                $household->default_currency ?? $user->default_currency,
                $tags,
                $userCorrections,
                $globalCorrections,
            );

            $parsed['whisper_transcript'] = $text;

            return response()->json(['data' => $parsed]);
        } catch (ExpenseParseException) {
            return response()->json([
                'message' => __('errors.ai_parse_failed'),
            ], 422);
        } catch (AiUsageLimitExceededException) {
            return response()->json([
                'message' => __('errors.ai_usage_limit_reached'),
            ], 429);
        } catch (DailyVoiceLimitExceededException) {
            return response()->json([
                'message' => __('errors.daily_voice_limit_reached'),
            ], 429);
        }
    }
}
