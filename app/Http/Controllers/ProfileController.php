<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DailyGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function show()
    {
        $user = Auth::user();

        // Calculate statistics
        $stats = [
            'quran_read_percentage' => $user->quran_progress_percentage,
            'quran_memorized_percentage' => $user->memorization_progress_percentage,
            'total_prayers' => $user->prayerLogs()->whereRaw('"is_completed" = 1')->count(),
            'prayer_streak' => $user->prayer_streak,
            'total_points' => $user->total_points,
            'verses_read' => $user->verseProgress()->whereRaw('"is_read" = 1')->count(),
            'verses_understood' => $user->verseProgress()->whereRaw('"is_understood" = 1')->count(),
            'verses_memorized' => $user->verseProgress()->whereRaw('"is_memorized" = 1')->count(),
        ];

        // Get today's goal
        $goalDate = now()->toDateString();
        
        try {
            $todayGoal = DailyGoal::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'goal_date' => $goalDate,
                ],
                [
                    'target_verses' => 5,
                    'verses_completed' => 0,
                    'all_prayers_completed' => false,
                ]
            );
            $todayGoal->updateVerseProgress();
            $todayGoal->updatePrayerStatus();
        } catch (\Throwable $e) {
            \Log::error('Dashboard daily goal creation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user->id,
                'goal_date' => $goalDate,
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, "[ProfileController::show] Daily Goal Error - {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
        }

        // Get recent achievements
        $recentVerses = $user->verseProgress()
            ->whereRaw('"is_read" = 1')
            ->orderBy('read_at', 'desc')
            ->limit(5)
            ->with('verse.surah')
            ->get();

        return view('profile.show', compact('user', 'stats', 'todayGoal', 'recentVerses'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update Islamic preferences
     */
    public function updateIslamicPreferences(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'calculation_method' => 'nullable|in:MuslimWorldLeague,Egyptian,Karachi,UmmAlQura,Dubai,MoonsightingCommittee,NorthAmerica,Kuwait,Qatar,Singapore,Tehran,Turkey',
            'madhab' => 'nullable|in:Shafi,Hanafi',
            'timezone' => 'nullable|string|max:50',
            'prayer_notifications' => 'boolean',
            'reminder_minutes' => 'nullable|integer|min:0|max:60',
            'quran_translation' => 'nullable|in:english,arabic,both',
            'arabic_text_size' => 'nullable|in:small,medium,large',
            'daily_verse_goal' => 'nullable|integer|min:1|max:100',
            'enable_night_mode' => 'boolean',
            'auto_mark_prayers' => 'boolean',
            'congregation_points_bonus' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Islamic preferences updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password updated successfully!');
    }

    /**
     * Update location
     */
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
        ]);

        Auth::user()->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Set daily goal
     */
    public function setDailyGoal(Request $request)
    {
        $validated = $request->validate([
            'target_verses' => 'required|integer|min:1|max:100',
        ]);

        $goalDate = now()->toDateString();

        try {
            $goal = DailyGoal::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'goal_date' => $goalDate,
                ],
                $validated
            );

            return redirect()->route('profile.show')->with('success', 'Daily goal updated!');
        } catch (\Throwable $e) {
            \Log::error('Daily goal update failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id(),
                'goal_date' => $goalDate,
                'validated_data' => $validated,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('profile.show')->withErrors("Daily Goal Update Error - {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
        }
    }

    /**
     * Dashboard view
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get today's stats
        $todayPrayers = $user->today_prayer_status;
        $goalDate = now()->toDateString();
        
        try {
            $todayGoal = DailyGoal::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'goal_date' => $goalDate,
                ],
                [
                    'target_verses' => 5,
                ]
            );
            $todayGoal->updateVerseProgress();
            $todayGoal->updatePrayerStatus();
        } catch (\Throwable $e) {
            \Log::error('Dashboard daily goal update failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user->id,
                'goal_date' => $goalDate,
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, "[ProfileController::dashboard] Daily Goal Error - {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
        }

        // Get weekly progress
        $weeklyPrayers = $user->prayerLogs()
            ->whereRaw('"prayer_date" BETWEEN \'' . now()->startOfWeek()->toDateString() . '\' AND \'' . now()->endOfWeek()->toDateString() . '\'')
            ->whereRaw('"is_completed" = 1')
            ->count();

        $todayCompletedPrayers = $user->prayerLogs()
            ->whereRaw('"prayer_date" = \'' . now()->toDateString() . '\'')
            ->whereRaw('"is_completed" = 1')
            ->count();

        $monthlyPrayers = $user->prayerLogs()
            ->whereRaw('"prayer_date" BETWEEN \'' . now()->startOfMonth()->toDateString() . '\' AND \'' . now()->endOfMonth()->toDateString() . '\'')
            ->whereRaw('"is_completed" = 1')
            ->count();

        $currentStreak = $user->prayer_streak ?? 0;

        $weeklyVerses = $user->verseProgress()
            ->whereRaw('"read_at" BETWEEN \'' . now()->startOfWeek()->toDateTimeString() . '\' AND \'' . now()->endOfWeek()->toDateTimeString() . '\'')
            ->whereRaw('"is_read" = 1')
            ->count();

        // Get due reviews
        $dueReviews = $user->verseProgress()
            ->whereRaw('"is_memorized" = 1')
            ->whereRaw('"next_review_at" <= \'' . now()->toDateTimeString() . '\'')
            ->count();

        return view('dashboard', compact(
            'user',
            'todayPrayers',
            'todayGoal',
            'weeklyPrayers',
            'weeklyVerses',
            'dueReviews',
            'todayCompletedPrayers',
            'monthlyPrayers',
            'currentStreak'
        ));
    }
}
