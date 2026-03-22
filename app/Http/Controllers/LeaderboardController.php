<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display the leaderboard
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $timeframe = $request->input('timeframe', 'all-time');
        $category = $request->input('category', 'overall');

        // Build query based on category
        switch ($category) {
            case 'quran':
                $users = $this->getQuranLeaderboard($timeframe);
                break;
            case 'prayers':
                $users = $this->getPrayerLeaderboard($timeframe);
                break;
            case 'streak':
                $users = $this->getStreakLeaderboard();
                break;
            default:
                $users = $this->getOverallLeaderboard($timeframe);
        }

        // Find current user's rank
        $userRank = $users->search(function ($user) use ($currentUser) {
            return $user->id === $currentUser->id;
        });
        $userRank = $userRank !== false ? $userRank + 1 : null;

        return view('leaderboard.index', compact('users', 'currentUser', 'userRank', 'timeframe', 'category'));
    }

    /**
     * Get overall leaderboard
     */
    private function getOverallLeaderboard($timeframe)
    {
        return User::orderBy('total_points', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                return $user;
            });
    }

    /**
     * Get Quran progress leaderboard
     */
    private function getQuranLeaderboard($timeframe)
    {
        return User::withCount([
            'verseProgress as read_count' => function ($query) {
                $query->whereRaw('"is_read" = 1');
            },
            'verseProgress as memorized_count' => function ($query) {
                $query->where('is_memorized', true);
            }
        ])
            ->orderBy('memorized_count', 'desc')
            ->orderBy('read_count', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                $user->score = ($user->memorized_count * 5) + $user->read_count;
                return $user;
            });
    }

    /**
     * Get prayer completion leaderboard
     */
    private function getPrayerLeaderboard($timeframe)
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        
        $query = User::withCount([
            'prayerLogs as prayers_completed' => function ($query) use ($timeframe, $monthStart, $monthEnd) {
                $query->where('is_completed', true);
                if ($timeframe === 'month') {
                    $query->whereBetween('prayer_date', [$monthStart, $monthEnd]);
                } elseif ($timeframe === 'week') {
                    $query->whereBetween('prayer_date', [now()->startOfWeek(), now()->endOfWeek()]);
                }
            },
            'prayerLogs as prayers_on_time' => function ($query) use ($timeframe, $monthStart, $monthEnd) {
                $query->where('is_completed', true)->where('on_time', true);
                if ($timeframe === 'month') {
                    $query->whereBetween('prayer_date', [$monthStart, $monthEnd]);
                } elseif ($timeframe === 'week') {
                    $query->whereBetween('prayer_date', [now()->startOfWeek(), now()->endOfWeek()]);
                }
            }
        ]);

        return $query->orderBy('prayers_completed', 'desc')
            ->orderBy('prayers_on_time', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                return $user;
            });
    }

    /**
     * Get prayer streak leaderboard
     */
    private function getStreakLeaderboard()
    {
        return User::orderBy('prayer_streak', 'desc')
            ->orderBy('total_points', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                return $user;
            });
    }

    /**
     * Get user's detailed stats
     */
    public function userStats($userId)
    {
        $user = User::findOrFail($userId);
        $currentUser = Auth::user();

        $stats = [
            'total_points' => $user->total_points,
            'prayer_streak' => $user->prayer_streak,
            'verses_read' => $user->verseProgress()->whereRaw('"is_read" = 1')->count(),
            'verses_memorized' => $user->verseProgress()->where('is_memorized', true)->count(),
            'prayers_completed' => $user->prayerLogs()->where('is_completed', true)->count(),
            'prayers_on_time' => $user->prayerLogs()->where('is_completed', true)->where('on_time', true)->count(),
        ];

        return view('leaderboard.user-stats', compact('user', 'stats', 'currentUser'));
    }
}
