@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">As-salamu alaykum, {{ $user->name }}!</h1>
        <p class="text-gray-600 mt-1">May your day be blessed with faith and productivity.</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Points -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <i class="fas fa-coins text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Points</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($user->total_points) }}</p>
                </div>
            </div>
        </div>

        <!-- Prayer Streak -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                    <i class="fas fa-fire text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Prayer Streak</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $user->prayer_streak }} days</p>
                </div>
            </div>
        </div>

        <!-- Weekly Prayers -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-prayer-hands text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Week</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $weeklyPrayers }}/35</p>
                </div>
            </div>
        </div>

        <!-- Weekly Verses -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <i class="fas fa-book-quran text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Verses Read</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $weeklyVerses }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Prayers -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-prayer-hands text-indigo-600 mr-2"></i>
                    Today's Prayers
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($todayPrayers as $prayerName => $prayer)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $prayer['completed'] ? 'bg-green-100' : 'bg-gray-100' }}">
                                <i class="fas fa-{{ $prayer['completed'] ? 'check' : 'clock' }} {{ $prayer['completed'] ? 'text-green-600' : 'text-gray-400' }}"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 capitalize">{{ $prayerName }}</p>
                                @if($prayer['on_time'])
                                <p class="text-sm text-green-600"><i class="fas fa-star"></i> On time</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            @if($prayer['completed'])
                            <span class="text-green-600 font-medium">+{{ $prayer['points'] }} pts</span>
                            @else
                            <a href="{{ route('prayers.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Mark Complete</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Daily Goal -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-bullseye text-indigo-600 mr-2"></i>
                    Today's Goal
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-gray-700">Verses Progress</span>
                        <span class="text-gray-600">{{ $todayGoal->verses_completed }}/{{ $todayGoal->target_verses }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $todayGoal->progress_percentage }}%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">All Prayers</span>
                        @if($todayGoal->all_prayers_completed)
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        @else
                        <i class="fas fa-circle text-gray-300 text-xl"></i>
                        @endif
                    </div>
                </div>

                @if($dueReviews > 0)
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        You have <strong>{{ $dueReviews }}</strong> verses due for review
                    </p>
                    <a href="{{ route('quran.reviews') }}" class="text-sm text-yellow-700 font-medium hover:text-yellow-900 mt-2 inline-block">
                        Review now <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('prayers.index') }}" class="bg-gradient-to-r from-green-400 to-green-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-prayer-hands text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Track Prayers</h3>
            <p class="text-sm opacity-90 mt-1">View prayer times & log completion</p>
        </a>

        <a href="{{ route('quran.index') }}" class="bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-book-quran text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Read Quran</h3>
            <p class="text-sm opacity-90 mt-1">Track your reading progress</p>
        </a>

        <a href="{{ route('leaderboard.index') }}" class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-trophy text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Leaderboard</h3>
            <p class="text-sm opacity-90 mt-1">See how you rank globally</p>
        </a>
    </div>
</div>
@endsection
