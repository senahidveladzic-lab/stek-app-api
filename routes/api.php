<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\ExpenseVoiceController;
use App\Http\Controllers\Api\V1\GoogleAuthController;
use App\Http\Controllers\Api\V1\HouseholdController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\UserSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/google', GoogleAuthController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::patch('user/locale', [UserSettingsController::class, 'updateLocale']);
        Route::patch('user/currency', [UserSettingsController::class, 'updateCurrency']);

        Route::middleware('subscribed')->group(function () {
            Route::post('expenses/voice', [ExpenseVoiceController::class, 'store'])->middleware('throttle:60,1');
            Route::apiResource('expenses', ExpenseController::class)->except(['show'])->names('api.expenses');
            Route::get('dashboard/summary', [DashboardController::class, 'summary']);
            Route::get('categories', [CategoryController::class, 'index']);
            Route::apiResource('tags', TagController::class)->except(['show'])->names('api.tags');

            Route::get('budgets', [BudgetController::class, 'index']);
            Route::post('budgets', [BudgetController::class, 'store']);

            Route::get('household', [HouseholdController::class, 'show']);
            Route::patch('household', [HouseholdController::class, 'update']);
            Route::post('household/invite', [HouseholdController::class, 'invite']);
            Route::delete('household/members/{user}', [HouseholdController::class, 'removeMember']);
            Route::post('household/invite/{token}/accept', [HouseholdController::class, 'acceptInvitation']);
        });
    });
});
