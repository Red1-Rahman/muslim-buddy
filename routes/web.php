<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PrayerController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ZakatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Health check - diagnose database/app status
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'ok', 'database' => 'connected'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'database' => 'disconnected',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// Debug test - try to fetch a user like dashboard would
Route::get('/debug/test', function () {
    try {
        $user = \App\Models\User::first();
        return response()->json([
            'status' => 'ok',
            'user' => $user ? $user->toArray() : 'no users',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// Debug test - try authenticated user query like dashboard does
Route::middleware(['auth'])->get('/debug/dashboard-test', function () {
    try {
        $user = auth()->user();
        return response()->json(['status' => 'ok', 'step' => 1, 'user_id' => $user->id], 200);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'step' => 1, 'error' => $e->getMessage()], 500);
    }
});

Route::middleware(['auth'])->get('/debug/prayer-status-test', function () {
    try {
        $user = auth()->user();
        $status = $user->today_prayer_status;
        return response()->json(['status' => 'ok', 'prayer_status' => $status], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

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
        Route::put('/islamic-preferences', [ProfileController::class, 'updateIslamicPreferences'])->name('islamic-preferences');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/location', [ProfileController::class, 'updateLocation'])->name('location');
        Route::post('/daily-goal', [ProfileController::class, 'setDailyGoal'])->name('daily-goal');
    });

    // Prayer routes
    Route::prefix('prayers')->name('prayers.')->group(function () {
        Route::get('/', [PrayerController::class, 'index'])->name('index');
        Route::get('/test', [PrayerController::class, 'test'])->name('test');
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

    // Zakat routes
    Route::get('/zakat', [ZakatController::class, 'calculator'])->name('zakat.calculator');
    Route::post('/zakat/mark-paid', [ZakatController::class, 'markPaid'])->name('zakat.markPaid');
});

// Hadith routes (some public, some require auth)
use App\Http\Controllers\HadithController;

Route::prefix('hadith')->name('hadith.')->group(function () {
    // Public hadith browsing
    Route::get('/', [HadithController::class, 'index'])->name('index');
    Route::get('/{hadith}', [HadithController::class, 'show'])->name('show');
    Route::get('/search/ajax', [HadithController::class, 'search'])->name('search');
    
    // Authenticated hadith features
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard/progress', [HadithController::class, 'dashboard'])->name('dashboard');
        Route::post('/{hadith}/progress', [HadithController::class, 'updateProgress'])->name('update-progress');
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
