@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-trophy text-yellow-500"></i> Leaderboard
        </h1>
        <p class="text-gray-600 mt-1">See how you rank among the Muslim Buddy community</p>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6">
                <a href="{{ route('leaderboard.index', ['category' => 'overall', 'timeframe' => $timeframe]) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ $category === 'overall' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Overall
                </a>
                <a href="{{ route('leaderboard.index', ['category' => 'quran', 'timeframe' => $timeframe]) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ $category === 'quran' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Quran Progress
                </a>
                <a href="{{ route('leaderboard.index', ['category' => 'prayers', 'timeframe' => $timeframe]) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ $category === 'prayers' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Prayers
                </a>
                <a href="{{ route('leaderboard.index', ['category' => 'streak', 'timeframe' => $timeframe]) }}" 
                   class="py-4 px-1 border-b-2 font-medium text-sm {{ $category === 'streak' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Streaks
                </a>
            </nav>
        </div>
        
        @if($category !== 'streak')
        <div class="px-6 py-3 border-b border-gray-200">
            <div class="flex space-x-4">
                <a href="{{ route('leaderboard.index', ['category' => $category, 'timeframe' => 'all-time']) }}" 
                   class="px-3 py-1 rounded-full text-sm {{ $timeframe === 'all-time' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-600 hover:text-gray-900' }}">
                    All Time
                </a>
                <a href="{{ route('leaderboard.index', ['category' => $category, 'timeframe' => 'month']) }}" 
                   class="px-3 py-1 rounded-full text-sm {{ $timeframe === 'month' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-600 hover:text-gray-900' }}">
                    This Month
                </a>
                <a href="{{ route('leaderboard.index', ['category' => $category, 'timeframe' => 'week']) }}" 
                   class="px-3 py-1 rounded-full text-sm {{ $timeframe === 'week' ? 'bg-indigo-100 text-indigo-800' : 'text-gray-600 hover:text-gray-900' }}">
                    This Week
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Current User Rank -->
    @if($userRank)
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white mb-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold">Your Rank</h2>
            <p class="text-4xl font-bold mt-2">#{{ $userRank }}</p>
            <p class="text-indigo-100 mt-1">out of {{ $users->count() }} users</p>
        </div>
    </div>
    @endif

    <!-- Leaderboard List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                @switch($category)
                    @case('quran')
                        Quran Progress Leaders
                        @break
                    @case('prayers')
                        Prayer Champions
                        @break
                    @case('streak')
                        Longest Streaks
                        @break
                    @default
                        Top Performers
                @endswitch
            </h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @foreach($users->take(50) as $user)
            <div class="px-6 py-4 flex items-center {{ $user->id === $currentUser->id ? 'bg-indigo-50' : '' }}">
                <!-- Rank -->
                <div class="flex-shrink-0 w-12 text-center">
                    @if($user->rank <= 3)
                        <i class="fas fa-trophy {{ $user->rank === 1 ? 'text-yellow-500' : ($user->rank === 2 ? 'text-gray-400' : 'text-yellow-600') }} text-xl"></i>
                    @else
                        <span class="text-lg font-bold text-gray-600">#{{ $user->rank }}</span>
                    @endif
                </div>

                <!-- User Info -->
                <div class="flex-1 ml-4">
                    <div class="flex items-center">
                        <h4 class="text-sm font-medium text-gray-900">
                            {{ $user->name }}
                            @if($user->id === $currentUser->id)
                            <span class="ml-2 px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full">You</span>
                            @endif
                        </h4>
                    </div>
                    @if($user->location_name)
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $user->location_name }}
                    </p>
                    @endif
                </div>

                <!-- Stats -->
                <div class="text-right">
                    @switch($category)
                        @case('quran')
                            <p class="text-sm font-medium text-gray-900">{{ $user->score ?? 0 }} points</p>
                            <p class="text-xs text-gray-500">{{ $user->memorized_count ?? 0 }} memorized, {{ $user->read_count ?? 0 }} read</p>
                            @break
                        @case('prayers')
                            <p class="text-sm font-medium text-gray-900">{{ $user->prayers_completed ?? 0 }} prayers</p>
                            <p class="text-xs text-gray-500">{{ $user->prayers_on_time ?? 0 }} on time</p>
                            @break
                        @case('streak')
                            <p class="text-sm font-medium text-gray-900">{{ $user->prayer_streak }} days</p>
                            <p class="text-xs text-gray-500">{{ number_format($user->total_points) }} total points</p>
                            @break
                        @default
                            <p class="text-sm font-medium text-gray-900">{{ number_format($user->total_points) }} points</p>
                            <p class="text-xs text-gray-500">{{ $user->prayer_streak }} day streak</p>
                    @endswitch
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Motivation Section -->
    <div class="mt-8 bg-gradient-to-r from-green-400 to-teal-500 rounded-lg shadow-lg p-6 text-white">
        <h3 class="text-xl font-bold mb-2">Keep Growing!</h3>
        <p class="text-sm opacity-90">
            @if($userRank && $userRank > 10)
                You're doing great! Keep practicing to climb higher in the rankings.
            @elseif($userRank && $userRank <= 10)
                Amazing! You're in the top 10. Your dedication is inspiring others.
            @else
                Start your journey today by tracking prayers and reading the Quran.
            @endif
        </p>
        <div class="mt-4 flex space-x-4">
            <a href="{{ route('prayers.index') }}" class="bg-white bg-opacity-20 px-4 py-2 rounded-md text-sm font-medium hover:bg-opacity-30 transition">
                Track Prayers
            </a>
            <a href="{{ route('quran.index') }}" class="bg-white bg-opacity-20 px-4 py-2 rounded-md text-sm font-medium hover:bg-opacity-30 transition">
                Read Quran
            </a>
        </div>
    </div>
</div>
@endsection