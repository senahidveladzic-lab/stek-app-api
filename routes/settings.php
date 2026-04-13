<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\BillingController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::inertia('settings/appearance', 'settings/appearance')->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('settings/billing', [BillingController::class, 'show'])->name('billing.show');
    Route::get('settings/billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::post('settings/billing/swap', [BillingController::class, 'swap'])->name('billing.swap');
    Route::post('settings/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('settings/billing/resume', [BillingController::class, 'resume'])->name('billing.resume');
    Route::get('settings/billing/payment-method', [BillingController::class, 'updatePaymentMethod'])->name('billing.payment-method');
});
