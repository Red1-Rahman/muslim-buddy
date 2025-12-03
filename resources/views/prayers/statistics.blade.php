@extends('layouts.app')

@section('title', 'Prayer Statistics')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-emerald-900 mb-2">📊 Prayer Statistics</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Track your prayer journey with detailed insights and progress
            </p>
        </div>

        <!-- Back to Prayers -->
        <div class="mb-6">
            <a href="{{ route('prayers.index') }}" 
               class="text-emerald-600 hover:text-emerald-800 flex items-center">
                ← Back to Prayers
            </a>
        </div>

        <!-- Summary Stats -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Today</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['today_completed'] ?? 0 }}/5</p>
                        <p class="text-sm text-gray-500">prayers completed</p>
                    </div>
                    <div class="text-4xl">🕌</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">This Week</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['week_completed'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">total prayers</p>
                    </div>
                    <div class="text-4xl">📅</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">This Month</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['this_month'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">total prayers</p>
                    </div>
                    <div class="text-4xl">📊</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Current Streak</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['current_streak'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500">days</p>
                    </div>
                    <div class="text-4xl">🔥</div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="grid lg:grid-cols-2 gap-8 mb-8">
            <!-- Prayer Completion Rate -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Prayer Completion Rate</h3>
                
                @php
                    $totalPrayers = $stats['total_prayers'] ?? 0;
                    $completedPrayers = $totalPrayers; // Since we're only counting completed prayers
                    $completionRate = $totalPrayers > 0 ? 100 : 0; // 100% since we only count completed ones
                @endphp

                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Overall Completion</span>
                        <span class="text-sm font-medium text-emerald-600">{{ $completionRate }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-3 rounded-full transition-all duration-300"
                             style="width: {{ min($completionRate, 100) }}%"></div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Prayers Completed</span>
                        <span class="font-semibold">{{ $totalPrayers }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">On Time</span>
                        <span class="font-semibold text-green-600">{{ $stats['prayers_on_time'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">In Congregation</span>
                        <span class="font-semibold text-blue-600">{{ $stats['prayers_in_congregation'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Monthly Progress</h3>
                
                <div class="space-y-3">
                    @for($month = 1; $month <= 12; $month++)
                        @php
                            $monthName = date('M', mktime(0, 0, 0, $month, 1));
                            $monthCount = $monthlyData->get($month, 0);
                            $maxCount = $monthlyData->max() ?: 1;
                            $percentage = round(($monthCount / $maxCount) * 100);
                        @endphp
                        <div class="flex items-center">
                            <div class="w-12 text-sm text-gray-600">{{ $monthName }}</div>
                            <div class="flex-1 mx-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full transition-all duration-300"
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="w-8 text-sm text-gray-800 font-medium">{{ $monthCount }}</div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Prayer Quality Analysis -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Prayer Quality Analysis</h3>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl mb-2">⏰</div>
                    <h4 class="font-semibold text-gray-800 mb-2">On Time</h4>
                    @php
                        $totalCompleted = $stats['total_prayers'] ?? 0;
                        $onTimeRate = $totalCompleted > 0 
                            ? round((($stats['prayers_on_time'] ?? 0) / $totalCompleted) * 100, 1)
                            : 0;
                    @endphp
                    <div class="text-2xl font-bold text-green-600">{{ $onTimeRate }}%</div>
                    <p class="text-sm text-gray-500">{{ $stats['prayers_on_time'] ?? 0 }} prayers</p>
                </div>

                <div class="text-center">
                    <div class="text-3xl mb-2">👥</div>
                    <h4 class="font-semibold text-gray-800 mb-2">In Congregation</h4>
                    @php
                        $congregationRate = $totalCompleted > 0 
                            ? round((($stats['prayers_in_congregation'] ?? 0) / $totalCompleted) * 100, 1)
                            : 0;
                    @endphp
                    <div class="text-2xl font-bold text-blue-600">{{ $congregationRate }}%</div>
                    <p class="text-sm text-gray-500">{{ $stats['prayers_in_congregation'] ?? 0 }} prayers</p>
                </div>

                <div class="text-center">
                    <div class="text-3xl mb-2">🕌</div>
                    <h4 class="font-semibold text-gray-800 mb-2">At Mosque</h4>
                    @php
                        $mosqueRate = $totalCompleted > 0 
                            ? round((($stats['mosque_prayers'] ?? 0) / $totalCompleted) * 100, 1)
                            : 0;
                    @endphp
                    <div class="text-2xl font-bold text-purple-600">{{ $mosqueRate }}%</div>
                    <p class="text-sm text-gray-500">{{ $stats['mosque_prayers'] ?? 0 }} prayers</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Recent Activity</h3>
            
            @if(isset($stats['recent_prayers']) && $stats['recent_prayers']->isNotEmpty())
                <div class="space-y-3">
                    @foreach($stats['recent_prayers']->take(10) as $prayer)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="text-2xl">
                                    @switch($prayer->prayer_name)
                                        @case('Fajr')
                                            🌅
                                            @break
                                        @case('Dhuhr')
                                            ☀️
                                            @break
                                        @case('Asr')
                                            🌤️
                                            @break
                                        @case('Maghrib')
                                            🌅
                                            @break
                                        @case('Isha')
                                            🌙
                                            @break
                                        @default
                                            🕌
                                    @endswitch
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ $prayer->prayer_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $prayer->prayer_date->format('M j, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($prayer->on_time)
                                    <span class="text-green-600 text-sm">⏰</span>
                                @endif
                                @if($prayer->in_congregation)
                                    <span class="text-blue-600 text-sm">👥</span>
                                @endif
                                @if($prayer->at_mosque)
                                    <span class="text-purple-600 text-sm">🕌</span>
                                @endif
                                <span class="text-emerald-600 text-sm">✓</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-4">📊</div>
                    <p>No prayer records found yet.</p>
                    <p class="text-sm mt-2">Start completing prayers to see your statistics here.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection