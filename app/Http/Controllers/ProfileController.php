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
            'total_prayers' => $user->prayerLogs()->where('is_completed', true)->count(),
            'prayer_streak' => $user->prayer_streak,
            'total_points' => $user->total_points,
            'verses_read' => $user->verseProgress()->where('is_read', true)->count(),
            'verses_understood' => $user->verseProgress()->where('is_understood', true)->count(),
            'verses_memorized' => $user->verseProgress()->where('is_memorized', true)->count(),
        ];

        // Get today's goal
        $todayGoal = DailyGoal::firstOrCreate(
            [
                'user_id' => $user->id,
                'goal_date' => now()->toDateString(),
            ],
            [
                'target_verses' => 5,
                'verses_completed' => 0,
                'all_prayers_completed' => false,
            ]
        );
        $todayGoal->updateVerseProgress();
        $todayGoal->updatePrayerStatus();

        // Get recent achievements
        $recentVerses = $user->verseProgress()
            ->where('is_read', true)
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
            'calculation_method' => 'nullable|string',
            'madhab' => 'nullable|in:Shafi,Hanafi',
            'timezone' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
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

        $goal = DailyGoal::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'goal_date' => now()->toDateString(),
            ],
            $validated
        );

        return redirect()->route('profile.show')->with('success', 'Daily goal updated!');
    }

    /**
     * Dashboard view
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get today's stats
        $todayPrayers = $user->today_prayer_status;
        $todayGoal = DailyGoal::firstOrCreate(
            [
                'user_id' => $user->id,
                'goal_date' => now()->toDateString(),
            ],
            [
                'target_verses' => 5,
            ]
        );
        $todayGoal->updateVerseProgress();
        $todayGoal->updatePrayerStatus();

        // Get weekly progress
        $weeklyPrayers = $user->prayerLogs()
            ->whereBetween('prayer_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('is_completed', true)
            ->count();

        $weeklyVerses = $user->verseProgress()
            ->whereBetween('read_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('is_read', true)
            ->count();

        // Get due reviews
        $dueReviews = $user->verseProgress()
            ->where('is_memorized', true)
            ->where('next_review_at', '<=', now())
            ->count();

        return view('dashboard', compact('user', 'todayPrayers', 'todayGoal', 'weeklyPrayers', 'weeklyVerses', 'dueReviews'));
    }
}
