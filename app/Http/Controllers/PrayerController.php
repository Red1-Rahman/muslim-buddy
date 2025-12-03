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

        // Calculate prayer times for today with proper timezone
        $userTimezone = $user->timezone ?? $this->detectUserTimezone($request) ?? 'UTC';
        $date = new DateTime($request->input('date', 'now'), new \DateTimeZone($userTimezone));
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
     * Test prayer times accuracy for different cities
     */
    public function test(Request $request)
    {
        $testCities = [
            [
                'name' => 'Dhaka, Bangladesh',
                'coordinates' => '23.8103°N, 90.4125°E',
                'lat' => 23.8103,
                'lon' => 90.4125,
                'timezone' => 'Asia/Dhaka',
                'method' => 'Karachi'
            ],
            [
                'name' => 'Mecca, Saudi Arabia',
                'coordinates' => '21.4225°N, 39.8262°E',
                'lat' => 21.4225,
                'lon' => 39.8262,
                'timezone' => 'Asia/Riyadh',
                'method' => 'UmmAlQura'
            ],
            [
                'name' => 'London, UK',
                'coordinates' => '51.5074°N, 0.1278°W',
                'lat' => 51.5074,
                'lon' => -0.1278,
                'timezone' => 'Europe/London',
                'method' => 'MuslimWorldLeague'
            ],
            [
                'name' => 'Gaza, Palestine',
                'coordinates' => '31.5017°N, 34.4668°E',
                'lat' => 31.5017,
                'lon' => 34.4668,
                'timezone' => 'Asia/Gaza',
                'method' => 'Egyptian'
            ],
            [
                'name' => 'Cairo, Egypt',
                'coordinates' => '30.0444°N, 31.2357°E',
                'lat' => 30.0444,
                'lon' => 31.2357,
                'timezone' => 'Africa/Cairo',
                'method' => 'Egyptian'
            ],
            [
                'name' => 'Istanbul, Turkey',
                'coordinates' => '41.0082°N, 28.9784°E',
                'lat' => 41.0082,
                'lon' => 28.9784,
                'timezone' => 'Europe/Istanbul',
                'method' => 'Turkey'
            ]
        ];

        $testResults = [];
        $today = new DateTime('now');

        foreach ($testCities as $city) {
            try {
                $coordinates = new Coordinates($city['lat'], $city['lon']);
                $date = new DateTime('now', new \DateTimeZone($city['timezone']));
                
                $methodName = lcfirst($city['method']);
                $calculationParams = CalculationMethod::$methodName();
                
                $prayerTimes = new PrayerTimes($coordinates, $date, $calculationParams);
                
                $city['times'] = [
                    'fajr' => $prayerTimes->fajr->format('H:i'),
                    'sunrise' => $prayerTimes->sunrise->format('H:i'),
                    'dhuhr' => $prayerTimes->dhuhr->format('H:i'),
                    'asr' => $prayerTimes->asr->format('H:i'),
                    'maghrib' => $prayerTimes->maghrib->format('H:i'),
                    'isha' => $prayerTimes->isha->format('H:i'),
                ];
                
                $city['current_prayer'] = $prayerTimes->currentPrayer($date);
                $city['is_forbidden_time'] = $prayerTimes->isForbiddenTime($date);
                
                $testResults[] = $city;
            } catch (\Exception $e) {
                $city['error'] = $e->getMessage();
                $testResults[] = $city;
            }
        }

        return view('prayers.test', compact('testResults'));
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
            'current_streak' => $user->prayer_streak ?? 0,
            'this_month' => PrayerLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereMonth('prayer_date', now()->month)
                ->count(),
            'recent_prayers' => PrayerLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->orderBy('prayer_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
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

    /**
     * Detect user's timezone from request headers or JavaScript
     */
    private function detectUserTimezone(Request $request): ?string
    {
        // Try to get timezone from request header (if set by JavaScript)
        $timezone = $request->header('X-User-Timezone');
        
        if ($timezone && $this->isValidTimezone($timezone)) {
            return $timezone;
        }

        // Try to detect from Accept-Language header and approximate
        $acceptLanguage = $request->header('Accept-Language', '');
        $languageMap = [
            'en-US' => 'America/New_York',
            'en-GB' => 'Europe/London',
            'en-CA' => 'America/Toronto',
            'en-AU' => 'Australia/Sydney',
            'ar-SA' => 'Asia/Riyadh',
            'tr-TR' => 'Europe/Istanbul',
            'ur-PK' => 'Asia/Karachi',
            'ms-MY' => 'Asia/Kuala_Lumpur',
            'id-ID' => 'Asia/Jakarta',
            'bn-BD' => 'Asia/Dhaka',
        ];

        foreach ($languageMap as $lang => $tz) {
            if (strpos($acceptLanguage, $lang) !== false) {
                return $tz;
            }
        }

        return null;
    }

    /**
     * Validate if timezone is valid
     */
    private function isValidTimezone(string $timezone): bool
    {
        return in_array($timezone, timezone_identifiers_list());
    }
}
