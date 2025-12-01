<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\LeaderboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes (handled by Laravel Breeze or similar)
require __DIR__.'/auth.php';

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/location', [ProfileController::class, 'updateLocation'])->name('location');
        Route::post('/daily-goal', [ProfileController::class, 'setDailyGoal'])->name('daily-goal');
    });

    // Prayer routes
    Route::prefix('prayers')->name('prayers.')->group(function () {
        Route::get('/', [PrayerController::class, 'index'])->name('index');
        Route::post('/{id}/complete', [PrayerController::class, 'complete'])->name('complete');
        Route::post('/{id}/toggle', [PrayerController::class, 'toggle'])->name('toggle');
        Route::get('/statistics', [PrayerController::class, 'statistics'])->name('statistics');
    });

    // Quran routes
    Route::prefix('quran')->name('quran.')->group(function () {
        Route::get('/', [QuranController::class, 'index'])->name('index');
        Route::get('/surah/{surahNumber}', [QuranController::class, 'show'])->name('show');
        Route::get('/surah/{surahNumber}/verse/{verseNumber}', [QuranController::class, 'verse'])->name('verse');
        Route::post('/verse/{verseId}/read', [QuranController::class, 'markAsRead'])->name('mark-read');
        Route::post('/verse/{verseId}/understood', [QuranController::class, 'markAsUnderstood'])->name('mark-understood');
        Route::post('/verse/{verseId}/memorized', [QuranController::class, 'markAsMemorized'])->name('mark-memorized');
        Route::post('/verse/{verseId}/review', [QuranController::class, 'review'])->name('review');
        Route::get('/reviews', [QuranController::class, 'dueReviews'])->name('reviews');
        Route::get('/search', [QuranController::class, 'search'])->name('search');
        Route::get('/statistics', [QuranController::class, 'statistics'])->name('statistics');
    });

    // Leaderboard routes
    Route::prefix('leaderboard')->name('leaderboard.')->group(function () {
        Route::get('/', [LeaderboardController::class, 'index'])->name('index');
        Route::get('/user/{userId}', [LeaderboardController::class, 'userStats'])->name('user-stats');
    });
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    // Public API for prayer times
    Route::post('/prayer-times', [PrayerController::class, 'apiPrayerTimes'])->name('prayer-times');
    
    // Protected API routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', function (Illuminate\Http\Request $request) {
            return $request->user();
        });
    });
});
