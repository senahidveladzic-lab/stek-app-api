<?php

use App\Http\Controllers\Web\BudgetController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\GoogleAuthController;
use App\Http\Controllers\Web\HouseholdController;
use App\Http\Controllers\Web\SettingsLocaleController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::post('locale', function (\Illuminate\Http\Request $request) {
    $available = config('locale.available', ['bs', 'en']);
    $locale = $request->input('locale');
    if ($locale && in_array($locale, $available)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale');

Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::view('terms', 'terms')->name('terms');
Route::view('privacy', 'privacy')->name('privacy');
Route::view('about', 'about')->name('about');
Route::get('contact', [ContactController::class, 'show'])->name('contact');
Route::post('contact', [ContactController::class, 'send'])->name('contact.send')->middleware('throttle:5,1');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('expenses/voice', [ExpenseController::class, 'voice'])->name('expenses.voice');

    Route::patch('settings/locale', [SettingsLocaleController::class, 'updateLocale'])->name('settings.locale');
    Route::patch('settings/currency', [SettingsLocaleController::class, 'updateCurrency'])->name('settings.currency');

    Route::get('budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('budgets', [BudgetController::class, 'store'])->name('budgets.store');

    Route::get('household', [HouseholdController::class, 'show'])->name('household.show');
    Route::patch('household', [HouseholdController::class, 'update'])->name('household.update');
    Route::post('household/invite', [HouseholdController::class, 'invite'])->name('household.invite');
    Route::delete('household/members/{user}', [HouseholdController::class, 'removeMember'])->name('household.removeMember');
    Route::get('household/invite/{token}', [HouseholdController::class, 'acceptInvitation'])->name('household.acceptInvitation');
});

require __DIR__.'/settings.php';
