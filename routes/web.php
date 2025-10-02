<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Judge\ScoringController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Debug route for OAuth testing
Route::get('/debug-oauth', function () {
    $config = [
        'google_client_id' => config('services.google.client_id'),
        'google_redirect_uri' => config('services.google.redirect'),
        'app_url' => config('app.url'),
    ];
    
    return response()->json($config);
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Social Authentication Routes
Route::get('/auth/{provider}', [SocialiteController::class, 'redirectToProvider'])
    ->name('auth.provider');
    
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])
    ->name('auth.provider.callback');

// Judge Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::prefix('judge')->name('judge.')->group(function () {
        Route::get('/events', [ScoringController::class, 'index'])->name('events');
        Route::get('/events/{event}', [ScoringController::class, 'showEvent'])->name('event.show');
        Route::post('/events/{event}/scores', [ScoringController::class, 'store'])->name('scores.store');
        Route::get('/events/{event}/scores', [ScoringController::class, 'getScores'])->name('scores.get');
        Route::get('/events/{event}/results', [ScoringController::class, 'showResults'])->name('results');
    });
});
