<?php

namespace App\Http\Controllers;

use App\Models\PrayerLog;
use App\Services\Astronomy\Coordinates;
use App\Services\Prayer\CalculationMethod;
use App\Services\Prayer\PrayerTimes;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrayerController extends Controller
{
    /**
     * Display prayer times dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's coordinates or use default (Dhaka, Bangladesh)
        $coordinates = new Coordinates(
            $user->latitude ?? 23.8103,
            $user->longitude ?? 90.4125
        );

        // Get calculation method
        $methodName = $user->calculation_method ?? 'MuslimWorldLeague';
        $method = 'muslimWorldLeague';
        if (method_exists(CalculationMethod::class, lcfirst($methodName))) {
            $method = lcfirst($methodName);
        }
        $calculationParams = CalculationMethod::$method();
        $calculationParams->madhab = $user->madhab ?? 'Shafi';

        // Calculate prayer times for today
        $date = new DateTime($request->input('date', 'now'));
        $prayerTimes = new PrayerTimes($coordinates, $date, $calculationParams);

        // Get today's prayer logs
        $today = $date->format('Y-m-d');
        $prayerLogs = [];
        $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

        foreach ($prayers as $prayer) {
            $log = PrayerLog::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'prayer_date' => $today,
                    'prayer_name' => $prayer,
                ],
                [
                    'is_completed' => false,
                ]
            );
            $prayerLogs[$prayer] = $log;
        }

        return view('prayers.index', compact('prayerTimes', 'prayerLogs', 'user', 'date'));
    }

    /**
     * Mark prayer as completed
     */
    public function complete(Request $request, $id)
    {
        $log = PrayerLog::findOrFail($id);

        // Ensure the log belongs to the authenticated user
        if ($log->user_id !== Auth::id()) {
            abort(403);
        }

        $log->markAsCompleted(
            $request->boolean('on_time'),
            $request->boolean('in_congregation'),
            $request->boolean('at_mosque')
        );

        return redirect()->route('prayers.index')->with('success', 'Prayer marked as completed!');
    }

    /**
     * Toggle prayer completion
     */
    public function toggle(Request $request, $id)
    {
        $log = PrayerLog::findOrFail($id);

        if ($log->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($log->is_completed) {
            // Unmark
            $log->user->decrement('total_points', $log->points_earned);
            $log->update([
                'is_completed' => false,
                'completed_at' => null,
                'on_time' => false,
                'in_congregation' => false,
                'at_mosque' => false,
                'points_earned' => 0,
            ]);
        } else {
            // Mark as completed
            $log->markAsCompleted();
        }

        return response()->json([
            'success' => true,
            'is_completed' => $log->is_completed,
            'points' => $log->points_earned,
        ]);
    }

    /**
     * Get prayer times API
     */
    public function apiPrayerTimes(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'date' => 'nullable|date',
            'method' => 'nullable|string',
        ]);

        $coordinates = new Coordinates(
            $request->input('latitude'),
            $request->input('longitude')
        );

        $methodName = $request->input('method', 'muslimWorldLeague');
        if (!method_exists(CalculationMethod::class, $methodName)) {
            $methodName = 'muslimWorldLeague';
        }
        $calculationParams = CalculationMethod::$methodName();

        $date = new DateTime($request->input('date', 'now'));
        $prayerTimes = new PrayerTimes($coordinates, $date, $calculationParams);

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'coordinates' => $coordinates->toArray(),
            'prayer_times' => $prayerTimes->toArray(),
            'current_prayer' => $prayerTimes->currentPrayer(),
            'next_prayer' => $prayerTimes->nextPrayer(),
            'is_forbidden_time' => $prayerTimes->isForbiddenTime(),
        ]);
    }

    /**
     * Display prayer statistics
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'total_prayers' => PrayerLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->count(),
            'prayers_on_time' => PrayerLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->where('on_time', true)
                ->count(),
            'prayers_in_congregation' => PrayerLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->where('in_congregation', true)
                ->count(),
            'current_streak' => $user->prayer_streak,
            'this_month' => PrayerLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereMonth('prayer_date', now()->month)
                ->count(),
        ];

        // Get monthly prayer completion rate
        $monthlyData = PrayerLog::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereYear('prayer_date', now()->year)
            ->selectRaw('MONTH(prayer_date) as month, COUNT(*) as count')
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month');

        return view('prayers.statistics', compact('stats', 'monthlyData', 'user'));
    }
}
