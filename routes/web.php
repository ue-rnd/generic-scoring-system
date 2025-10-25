<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Judge\ScoringController;
use App\Http\Controllers\TokenScoringController;
use App\Http\Controllers\PublicViewController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Filament handles root (/) and /login routes automatically
// No need to define custom routes for these paths

// Debug route for OAuth testing
Route::get('/auth/{provider}', [SocialiteController::class, 'redirectToProvider'])
    ->name('auth.provider');
    
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])
    ->name('auth.provider.callback');

// Token-based Judge Scoring Routes (No Authentication Required)
Route::prefix('score')->name('score.')->group(function () {
    Route::get('/{token}', [TokenScoringController::class, 'showScoringInterface'])->name('judge');
    Route::post('/{token}', [TokenScoringController::class, 'store'])->name('store');
    Route::get('/{token}/scores', [TokenScoringController::class, 'getScores'])->name('get');
    Route::get('/{token}/results', [TokenScoringController::class, 'showResults'])->name('results');
});

// Admin Scoring Routes (Token-based, for quiz bee events)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/score/{token}', [\App\Http\Controllers\AdminScoringController::class, 'show'])->name('score.show');
    Route::post('/score/{token}', [\App\Http\Controllers\AdminScoringController::class, 'store'])->name('score.store');
    Route::get('/score/{token}/live', [\App\Http\Controllers\AdminScoringController::class, 'getLive'])->name('score.live');
});

// Public Viewing Routes (No Authentication Required)
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/event/{token}', [PublicViewController::class, 'show'])->name('view');
    Route::get('/event/{token}/live', [PublicViewController::class, 'getLiveResults'])->name('live');
    Route::get('/event/{token}/contestant/{contestant}', [PublicViewController::class, 'getContestantBreakdown'])->name('contestant');
});

// Judge Routes (require authentication) - Legacy support
Route::middleware(['auth'])->group(function () {
    Route::prefix('judge')->name('judge.')->group(function () {
        Route::get('/events', [ScoringController::class, 'index'])->name('events');
        Route::get('/events/{event}', [ScoringController::class, 'showEvent'])->name('event.show');
        Route::post('/events/{event}/scores', [ScoringController::class, 'store'])->name('scores.store');
        Route::get('/events/{event}/scores', [ScoringController::class, 'getScores'])->name('scores.get');
        Route::get('/events/{event}/results', [ScoringController::class, 'showResults'])->name('results');
    });
});
